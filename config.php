<?php
function loadEnv($path) {
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Charger les variables d'environnement
loadEnv(__DIR__ . '/.env');

// Configuration
return [
    'google_maps_api_key' => getenv('GOOGLE_MAPS_API_KEY')
]; 