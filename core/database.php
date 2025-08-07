<?php
// Core Database Connection File

// Include the configuration file which contains DB credentials
// The '@' suppresses warnings if the file doesn't exist, which we handle in index.php
@include_once __DIR__ . '/../app/config.php';

// --- Database Connection ---
try {
    // Check if the constants are defined (i.e., if config.php was included and is valid)
    if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
        // This case is primarily handled by the redirect in index.php, but it's a good fallback.
        // We don't throw an error here to avoid breaking the initial redirect check.
        $db = null;
    } else {
        // Establish the database connection using MySQLi
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check for connection errors
        if ($db->connect_error) {
            // In a real application, you might log this error instead of displaying it
            throw new Exception("Database connection failed: " . $db->connect_error);
        }

        // Set the character set to utf8mb4 for full Unicode support
        $db->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    // Display a generic error message to the user for security
    // The actual error would be logged for the administrator
    die("Error: Could not connect to the database. Please check your configuration.");
}

// The $db object is now available for use in any script that includes this file.
?>
