# Paramètres d'URL du formulaire de devis

Le formulaire (`https://devis.cityrecyclage.com/`) accepte des paramètres qui
présélectionnent la catégorie et ouvrent directement la bonne section.
Le lien s'arrête à la **catégorie** : aucun sous-type (DEEE, benne, chantier)
n'est présélectionné, le visiteur choisit lui-même sa tuile avant de voir les
contenants.
Une valeur absente ou inconnue laisse le formulaire dans son état par défaut.
Majuscules, accents et tirets sont tolérés (`Ferrailles`, `dechets-verts`).

## `?besoin=` — catégorie

| Valeur | « Je recycle » | Section ouverte |
|---|---|---|
| `deee` (alias `d3e`) | DEEE / D3E | choix du lieu de récupération |
| `informatique`, `cartouches` | DEEE / D3E (alias conservés, **sans** sous-type) | choix du lieu de récupération |
| `archives` | Destruction d'archives | choix du lieu |
| `ferraille` (alias `ferrailles`, `metaux`) | Déchets non dangereux + tuile Ferrailles | contenants ferrailles (les autres tuiles restent visibles) |
| `bureau` | Déchets de bureau / 5 flux | contenants bureau |
| `mobilier` | Mobilier de bureau / DEA | estimation du volume + camions |
| `debarras` | Débarrasser tous types de local | estimation du volume + camions |
| `benne` | Louer une benne | type de benne |
| `chantier` | Déchets de chantier | tuiles DIB / bois / plâtre / gravats |
| `dnd` | Déchets non dangereux | tuiles |
| `cartons`, `papiers`, `plastiques`, `palettes`, `encombrants`, `bois`, `dib`, `dechets_verts` | Déchets non dangereux + tuile correspondante | contenants |

Les valeurs internes du select (`destruction_archives`, `dechets_bureau`,
`mobilier_bureau`, `debarrasser_local`, `louer_benne`, `dechets_chantiers`,
`dechets_non_dangereux`) sont aussi acceptées.

## `?lieu=` — DEEE et archives

- `site` (alias `domicile`, `oui`) : récupération sur place (option « Oui »)
- `depot` (alias `non`) : dépôt chez City Debarras

Pour le dépôt, les champs étage / ascenseur sont masqués (inutiles).

## `?type=` — ignoré

Depuis septembre 2026, `?type=` n'a plus aucun effet (DEEE, benne, chantier) :
un sous-type présélectionné faisait apparaître des contenants que le visiteur
n'avait pas choisis. Les anciens liens avec `type=` continuent de fonctionner
et s'arrêtent à la catégorie.

## Paramètres de campagne transmis

`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`,
`gbraid`, `wbraid` et `besoin` (sous le nom `landing_besoin`) sont recopiés
dans des champs cachés du formulaire et envoyés avec la demande. `invoiced.php`
ne les exploite pas encore : ils apparaissent seulement dans `devis.log` et dans
le log de `email.php`.

Ils ne sont transmis que s'ils figurent dans l'URL du formulaire. Les boutons
des landing pages doivent donc les relayer (le gclid arrive sur
citydebarras.fr, pas sur devis.cityrecyclage.com).

## Liens profonds des landing pages citydebarras.fr

| Landing | Lien du bouton « Devis » |
|---|---|
| https://citydebarras.fr/destruction-archives-lp | `https://devis.cityrecyclage.com/?besoin=archives` |
| https://citydebarras.fr/materiels-informatiques-lp | `https://devis.cityrecyclage.com/?besoin=deee` |
| https://citydebarras.fr/ferrailles-lp | `https://devis.cityrecyclage.com/?besoin=ferraille` |
| https://citydebarras.fr/dechets-bureau-lp | `https://devis.cityrecyclage.com/?besoin=bureau` |

Variantes un clic plus courtes, si la landing ne vise que la collecte sur place :

- `https://devis.cityrecyclage.com/?besoin=archives&lieu=site`
- `https://devis.cityrecyclage.com/?besoin=deee&lieu=site` (affiche directement les tuiles de type DEEE)

Avec les paramètres de campagne (exemple) :
`https://devis.cityrecyclage.com/?besoin=ferraille&utm_source=google&utm_medium=cpc&utm_campaign=ferrailles`

## Événements dataLayer

`window.dataLayer` reçoit, sans aucun conteneur GTM chargé :

| Événement | Paramètres | Moment |
|---|---|---|
| `devis_view` | `besoin` | chargement |
| `devis_step1_complete` | `category`, `via` (`suivant` / `camion`) | passage à l'étape 2 |
| `devis_step2_view` | `category` | affichage de l'étape 2 |
| `devis_submit_click` | `category`, `valid` | clic sur « Envoyer » |
