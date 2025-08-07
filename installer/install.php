<?php
// Installation Handler for PubAd
// This script processes the form submissions from the installer.

// Start a session to store data between steps
session_start();

// --- Configuration ---
$config_path = '../app/config.php';

// --- Helper Functions ---
function redirect($url) {
    header("Location: " . $url);
    exit;
}

function display_error($message) {
    // A simple error page. Could be enhanced.
    die("Installation Error: " . htmlspecialchars($message));
}


// --- Main Logic ---

// Prevent direct access to this script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Determine which step is being processed
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

if ($step === 2) {
    // --- Process Step 2: Database Configuration ---

    // Sanitize and retrieve POST data
    $db_host = filter_input(INPUT_POST, 'db_host', FILTER_SANITIZE_STRING);
    $db_name = filter_input(INPUT_POST, 'db_name', FILTER_SANITIZE_STRING);
    $db_user = filter_input(INPUT_POST, 'db_user', FILTER_SANITIZE_STRING);
    $db_pass = $_POST['db_pass']; // No standard filter, will be used as is for connection test

    // Store in session
    $_SESSION['db_host'] = $db_host;
    $_SESSION['db_name'] = $db_name;
    $_SESSION['db_user'] = $db_user;
    $_SESSION['db_pass'] = $db_pass;

    // --- Test Database Connection ---
    // Suppress errors to handle them manually
    @$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($mysqli->connect_error) {
        // Could pass this error message back to the form
        display_error("Database connection failed: " . $mysqli->connect_error);
    }

    // Connection successful
    $mysqli->close();

    // Redirect to the next step
    redirect('index.php?step=3');

} elseif ($step === 3) {
    // --- Process Step 3: Admin & Final Installation ---

    // Ensure we have DB credentials from the previous step
    if (empty($_SESSION['db_host']) || empty($_SESSION['db_name']) || empty($_SESSION['db_user'])) {
        display_error("Database credentials not found in session. Please start over.");
    }

    // Sanitize and retrieve admin user data
    $admin_user = filter_input(INPUT_POST, 'admin_user', FILTER_SANITIZE_STRING);
    $admin_email = filter_input(INPUT_POST, 'admin_email', FILTER_VALIDATE_EMAIL);
    $admin_pass = $_POST['admin_pass'];

    if (!$admin_email) {
        display_error("Invalid email address provided.");
    }
    if (empty($admin_pass)) {
        display_error("Admin password cannot be empty.");
    }

    // --- 1. Create config.php content ---
    $config_content = "<?php\n\n";
    $config_content .= "// Database Configuration\n";
    $config_content .= "define('DB_HOST', '" . $_SESSION['db_host'] . "');\n";
    $config_content .= "define('DB_NAME', '" . $_SESSION['db_name'] . "');\n";
    $config_content .= "define('DB_USER', '" . $_SESSION['db_user'] . "');\n";
    $config_content .= "define('DB_PASS', '" . addslashes($_SESSION['db_pass']) . "');\n";
    $config_content .= "\n// Site Settings (you can add more here)\n";
    $config_content .= "define('SITE_URL', '" . (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]');\n";

    // --- 2. Write config.php file ---
    if (file_put_contents($config_path, $config_content) === false) {
        display_error("Could not write to config file at: " . $config_path);
    }

    // --- 3. Create database tables and insert admin user ---
    @$mysqli = new mysqli($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass'], $_SESSION['db_name']);
    if ($mysqli->connect_error) {
        display_error("Failed to connect to database after creating config file. Error: " . $mysqli->connect_error);
    }

    // SQL to create users table
    $sql_create_table = "
    CREATE TABLE IF NOT EXISTS `users` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(50) NOT NULL UNIQUE,
      `email` VARCHAR(100) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (!$mysqli->query($sql_create_table)) {
        display_error("Error creating users table: " . $mysqli->error);
    }

    // Hash the admin password
    $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

    // Insert the admin user using a prepared statement
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        display_error("Failed to prepare statement: " . $mysqli->error);
    }

    $stmt->bind_param("sss", $admin_user, $admin_email, $hashed_password);
    if (!$stmt->execute()) {
        display_error("Error inserting admin user: " . $stmt->error);
    }

    $stmt->close();
    $mysqli->close();

    // --- 4. Clean up and redirect to success page ---
    session_destroy();
    redirect('index.php?step=4');

} else {
    // Invalid step
    redirect('index.php');
}
