<?php

// Autoloader for App classes
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../classes/' . str_replace('App\\', '', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Start session
\App\Auth::startSession();

// Initialize database (connects and creates tables if needed)
\App\Database::getInstance();

// Helper function for backward compatibility
function get_db()
{
    return \App\Database::getInstance();
}

?>

