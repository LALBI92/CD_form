<?php
/**
 * Proxy vers l'API Pappers.
 *
 * Le formulaire appelait Pappers directement en JavaScript, ce qui exposait la clé
 * API à tous les visiteurs. Ce proxy garde la clé côté serveur (.env) et ne renvoie
 * au navigateur que les trois champs dont le formulaire a besoin.
 *
 * Chaque appel à Pappers coûte un crédit, d'où deux garde-fous avant de sortir :
 *  - le SIREN est validé (9 chiffres + clé de Luhn), ce qui écarte les saisies erronées
 *  - les réponses sont mises en cache, un même SIREN ne coûtant ainsi qu'une fois
 */

header('Content-Type: application/json; charset=utf-8');

const CACHE_DIR = __DIR__ . '/cache/pappers';
const CACHE_TTL = 2592000; // 30 jours : une raison sociale ne bouge quasiment jamais

function repondre(int $code, array $data): void
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Un SIREN valide satisfait la clé de Luhn. Ce contrôle est gratuit et évite
 * de dépenser un crédit pour une saisie manifestement fautive.
 */
function sirenValide(string $siren): bool
{
    if (!preg_match('/^\d{9}$/', $siren)) {
        return false;
    }
    $somme = 0;
    for ($i = 0; $i < 9; $i++) {
        $chiffre = (int) $siren[8 - $i];
        if ($i % 2 === 1) {
            $chiffre *= 2;
            if ($chiffre > 9) {
                $chiffre -= 9;
            }
        }
        $somme += $chiffre;
    }
    return $somme % 10 === 0;
}

// --- Entrée -----------------------------------------------------------------

$siren = preg_replace('/\D/', '', $_GET['siren'] ?? '');

// Le champ est libellé SIREN mais certains saisissent le SIRET (14 chiffres) :
// les 9 premiers chiffres d'un SIRET sont précisément le SIREN.
if (strlen($siren) === 14) {
    $siren = substr($siren, 0, 9);
}

if (!sirenValide($siren)) {
    repondre(400, ['erreur' => 'siren_invalide', 'message' => 'Le SIREN doit comporter 9 chiffres valides.']);
}

// --- Cache ------------------------------------------------------------------

$fichierCache = CACHE_DIR . '/' . $siren . '.json';
if (is_readable($fichierCache) && (time() - filemtime($fichierCache)) < CACHE_TTL) {
    echo file_get_contents($fichierCache);
    exit;
}

// --- Appel Pappers ----------------------------------------------------------

require __DIR__ . '/config.php';
$cle = getenv('PAPPERS_API_KEY') ?: '';

if ($cle === '') {
    error_log('pappers.php : PAPPERS_API_KEY absente de .env');
    repondre(503, ['erreur' => 'non_configure', 'message' => 'Service momentanément indisponible.']);
}

$ch = curl_init('https://api.pappers.fr/v2/entreprise?siren=' . urlencode($siren));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => ['api-key: ' . $cle],
]);
$corps = curl_exec($ch);
$statut = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$erreurReseau = curl_error($ch);
curl_close($ch);

if ($corps === false || $erreurReseau !== '') {
    error_log('pappers.php : échec réseau — ' . $erreurReseau);
    repondre(502, ['erreur' => 'reseau', 'message' => 'Service momentanément indisponible.']);
}

if ($statut === 404) {
    repondre(404, ['erreur' => 'introuvable', 'message' => 'Aucune entreprise trouvée pour ce SIREN.']);
}

// 401 = clé invalide ou crédits épuisés. À tracer côté serveur : sans cela, la panne
// est invisible (c'est exactement ce qui s'est produit de décembre 2025 à juillet 2026).
if ($statut !== 200) {
    error_log('pappers.php : Pappers a répondu HTTP ' . $statut . ' — ' . substr((string) $corps, 0, 200));
    repondre(502, ['erreur' => 'api', 'message' => 'Service momentanément indisponible.']);
}

$data = json_decode($corps, true);
if (!is_array($data)) {
    error_log('pappers.php : réponse Pappers illisible');
    repondre(502, ['erreur' => 'api', 'message' => 'Service momentanément indisponible.']);
}

// On ne renvoie que le strict nécessaire, plutôt que de recopier toute la réponse Pappers.
$resultat = json_encode([
    'denomination'                   => $data['denomination'] ?? '',
    'numero_tva_intracommunautaire'  => $data['numero_tva_intracommunautaire'] ?? '',
    'entreprise_cessee'              => (bool) ($data['entreprise_cessee'] ?? false),
], JSON_UNESCAPED_UNICODE);

if (!is_dir(CACHE_DIR)) {
    @mkdir(CACHE_DIR, 0775, true);
}
@file_put_contents($fichierCache, $resultat, LOCK_EX);

echo $resultat;
