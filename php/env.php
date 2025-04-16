<?php
    function load_env() {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Skip comments
                }
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, '"'); // Remove surrounding quotes if present
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
    load_env();
?>