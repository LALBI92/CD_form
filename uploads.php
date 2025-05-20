<?php
// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le chemin du fichier de log
$logFile = __DIR__ . '/devis.log';

// Initialisation de la réponse
$response = ['success' => false, 'message' => '', 'files' => []];

// Début du traitement des fichiers téléchargés
error_log("=== Début du traitement des fichiers téléchargés ===\n", 3, $logFile);

if (isset($_FILES['file-upload']) && !empty($_FILES['file-upload']['name'][0])) {
    $uploadDir = __DIR__ . '/uploads/';

    // Vérifiez si le dossier de téléchargement existe, sinon créez-le
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $response['message'] = "Impossible de créer le dossier de téléchargement";
            error_log("Erreur : Impossible de créer le dossier de téléchargement : $uploadDir\n", 3, $logFile);
            return $response;
        }
    }

    $totalFiles = count($_FILES['file-upload']['name']);
    $successCount = 0;

    for ($i = 0; $i < $totalFiles; $i++) {
        $fileName = $_FILES['file-upload']['name'][$i];
        $fileTmpName = $_FILES['file-upload']['tmp_name'][$i];
        $fileError = $_FILES['file-upload']['error'][$i];

        error_log("Traitement du fichier : $fileName\n", 3, $logFile);

        if ($fileError === 0) {
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $targetFile = $uploadDir . $newFileName;

            // Log avant le déplacement du fichier
            error_log("Tentative de déplacement : $fileTmpName vers $targetFile\n", 3, $logFile);

            if (move_uploaded_file($fileTmpName, $targetFile)) {
                $successCount++;
                $response['files'][] = [
                    'name' => $fileName,
                    'status' => 'success',
                    'message' => 'Fichier téléchargé avec succès',
                    'path' => $targetFile
                ];
                error_log("Fichier déplacé avec succès : $targetFile\n", 3, $logFile);
            } else {
                $response['files'][] = [
                    'name' => $fileName,
                    'status' => 'error',
                    'message' => 'Erreur lors du déplacement du fichier'
                ];
                error_log("Erreur lors du déplacement du fichier : $fileTmpName vers $targetFile\n", 3, $logFile);
            }
        } else {
            $errorMessages = [
                1 => "Le fichier dépasse la limite upload_max_filesize",
                2 => "Le fichier dépasse la limite MAX_FILE_SIZE",
                3 => "Le fichier n'a été que partiellement téléchargé",
                4 => "Aucun fichier n'a été téléchargé",
                6 => "Dossier temporaire manquant",
                7 => "Échec de l'écriture du fichier sur le disque",
                8 => "Une extension PHP a arrêté le téléchargement"
            ];
            $errorMessage = $errorMessages[$fileError] ?? "Erreur inconnue";

            $response['files'][] = [
                'name' => $fileName,
                'status' => 'error',
                'message' => $errorMessage
            ];
            error_log("Erreur lors du téléchargement du fichier : $fileName, Message : $errorMessage (Code : $fileError)\n", 3, $logFile);
        }
    }

    if ($successCount > 0) {
        $response['success'] = true;
        $response['message'] = $successCount === $totalFiles
            ? "Tous les fichiers ont été téléchargés avec succès"
            : "$successCount sur $totalFiles fichiers ont été téléchargés avec succès";
    } else {
        $response['message'] = "Aucun fichier n'a pu être téléchargé";
    }
} else {
    $response['message'] = "Aucun fichier n'a été envoyé";
    error_log("Aucun fichier n'a été envoyé\n", 3, $logFile);
}

error_log("=== Fin du traitement des fichiers téléchargés ===\n", 3, $logFile);
return $response;

