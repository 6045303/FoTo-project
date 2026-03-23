<?php

spl_autoload_register(function (string $className): void {
    $baseDir = __DIR__ . '/classes/';
    $candidates = [
        $baseDir . $className . '.php',
        $baseDir . strtolower($className) . '.php',
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
