<?php
// Ignition Arcade Installer
// ---
// This script guides the user through the installation process, including
// server checks, database setup, and admin account creation.
// For security, this file will automatically be deleted upon successful installation.

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// --- Configuration & Constants ---
define('MIN_PHP_VERSION', '7.4.0');
define('REQUIRED_EXTENSIONS', ['mysqli', 'json', 'pdo']);
define('CONFIG_FILE', 'app/config.php');
define('SCHEMA_FILE', 'app/database/schema.sql');

// --- State Management ---
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Security Check: If installation is already complete, block access.
if (file_exists(CONFIG_FILE)) {
    die_gracefully("Installation Complete", "The configuration file (`" . CONFIG_FILE . "`) already exists. To prevent accidental re-installation, the installer has been disabled. Please delete the config file if you wish to run the installer again.");
}


// --- Main Installer Logic ---
switch ($step) {
    case 1:
        // Step 1: Welcome & Server Requirements Check
        render_page('Welcome & Requirements', function() {
            $checks = perform_server_checks();
            $all_ok = !$checks['errors'];

            echo '<h2>Welcome to Ignition Arcade!</h2>';
            echo '<p>This wizard will guide you through the installation. Let\'s start by checking server requirements.</p>';

            echo '<ul>';
            echo '<li>PHP Version >= ' . MIN_PHP_VERSION . ': ' . $checks['php_version']['icon'] . ' (Your version: ' . $checks['php_version']['version'] . ')</li>';
            foreach ($checks['extensions'] as $ext => $result) {
                echo '<li>PHP Extension: ' . $ext . ': ' . $result['icon'] . '</li>';
            }
            echo '</ul>';

            if ($all_ok) {
                echo '<p class="success">Congratulations! Your server meets all the requirements.</p>';
                echo '<a href="?step=2" class="button">Next: Database Setup &raquo;</a>';
            } else {
                echo '<p class="error">Your server does not meet the minimum requirements. Please resolve the issues above before continuing.</p>';
                echo '<a href="?step=1" class="button-disabled" disabled>Next</a>';
            }
        });
        break;

    case 2:
        // Step 2: Database Connection
        render_page('Database Setup', function() {
            ?>
            <h2>Database Configuration</h2>
            <p>Please provide your MySQL database credentials. The installer will attempt to connect and set up the necessary tables.</p>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            <form action="install.php?step=3" method="post">
                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>

                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" required>

                <label for="db_user">Database Username</label>
                <input type="text" id="db_user" name="db_user" required>

                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass">

                <button type="submit" class="button">Test Connection &raquo;</button>
            </form>
            <?php
        });
        break;

    case 3:
        // Step 3: Process Database & Show Admin Form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: install.php?step=2');
            exit;
        }

        // Store submitted data in session
        $_SESSION['db_host'] = $_POST['db_host'];
        $_SESSION['db_name'] = $_POST['db_name'];
        $_SESSION['db_user'] = $_POST['db_user'];
        $_SESSION['db_pass'] = $_POST['db_pass'];

        // Test the database connection
        $mysqli = @new mysqli($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass'], $_SESSION['db_name']);
        if ($mysqli->connect_error) {
            $_SESSION['error'] = "Database connection failed: " . $mysqli->connect_error;
            header('Location: install.php?step=2');
            exit;
        }
        $mysqli->close();

        // If connection is successful, proceed to admin account creation
        render_page('Create Admin Account', function() {
            ?>
            <h2>Create Your Admin Account</h2>
            <p class="success">Database connection was successful!</p>
            <p>Now, create the primary administrator account.</p>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            <form action="install.php?step=4" method="post">
                <label for="admin_user">Admin Username</label>
                <input type="text" id="admin_user" name="admin_user" required>

                <label for="admin_email">Admin Email</label>
                <input type="email" id="admin_email" name="admin_email" required>

                <label for="admin_pass">Admin Password</label>
                <input type="password" id="admin_pass" name="admin_pass" required>

                <button type="submit" class="button">Finalize Installation &raquo;</button>
            </form>
            <?php
        });
        break;

    case 4:
        // Step 4: Finalize Installation
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['db_host'])) {
            header('Location: install.php?step=2');
            exit;
        }

        // --- 1. Create Config File ---
        $config_content = generate_config_file();
        if (@file_put_contents(CONFIG_FILE, $config_content) === false) {
             die_gracefully("Error", "Could not write the configuration file (`" . CONFIG_FILE . "`). Please check file permissions and try again.");
        }

        // --- 2. Populate Database ---
        $mysqli = new mysqli($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_pass'], $_SESSION['db_name']);
        if ($mysqli->connect_error) {
             die_gracefully("Error", "Lost database connection. Please start over.");
        }

        $schema = file_get_contents(SCHEMA_FILE);
        if ($mysqli->multi_query($schema) === false) {
            die_gracefully("Database Error", "Failed to import the database schema. Error: " . $mysqli->error);
        }
        // Clear multi_query results
        while ($mysqli->more_results() && $mysqli->next_result()) {};

        // --- 3. Create Admin User ---
        $admin_user = trim($_POST['admin_user']);
        $admin_email = trim($_POST['admin_email']);
        $admin_pass = password_hash(trim($_POST['admin_pass']), PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO `users` (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param('sss', $admin_user, $admin_email, $admin_pass);

        if ($stmt->execute() === false) {
             die_gracefully("Database Error", "Failed to create admin user. Error: " . $stmt->error);
        }
        $stmt->close();
        $mysqli->close();

        // --- 4. Final Success Page ---
        render_page('Installation Successful!', function() {
            echo '<h2>Installation Complete!</h2>';
            echo '<p class="success">Ignition Arcade has been installed successfully.</p>';
            echo '<p><strong>For security reasons, this installer file has now been deleted.</strong></p>';
            echo '<h3>Next Steps:</h3>';
            echo '<ul>';
            echo '<li><a href="admin/">Login to your Admin Panel</a></li>';
            echo '<li><a href="index.php">View your Website</a></li>';
            echo '</ul>';
        });

        // --- 5. Clean Up ---
        session_destroy();
        @unlink(__FILE__); // Self-destruct
        break;

    default:
        header('Location: install.php?step=1');
        exit;
}


// --- Helper Functions ---

function perform_server_checks() {
    $results = ['errors' => false];

    // Check PHP Version
    $php_ok = version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=');
    $results['php_version'] = [
        'ok' => $php_ok,
        'icon' => $php_ok ? '<span class="success">&#10004;</span>' : '<span class="error">&#10006;</span>',
        'version' => PHP_VERSION
    ];
    if (!$php_ok) $results['errors'] = true;

    // Check Extensions
    $results['extensions'] = [];
    foreach (REQUIRED_EXTENSIONS as $ext) {
        $loaded = extension_loaded($ext);
        $results['extensions'][$ext] = [
            'ok' => $loaded,
            'icon' => $loaded ? '<span class="success">&#10004;</span>' : '<span class="error">&#10006;</span>'
        ];
        if (!$loaded) $results['errors'] = true;
    }

    return $results;
}

function generate_config_file() {
    $content = "<?php\n\n";
    $content .= "// Ignition Arcade Configuration\n";
    $content .= "// --- Generated by Installer ---\n\n";
    $content .= "define('DB_HOST', '" . addslashes($_SESSION['db_host']) . "');\n";
    $content .= "define('DB_NAME', '" . addslashes($_SESSION['db_name']) . "');\n";
    $content .= "define('DB_USER', '" . addslashes($_SESSION['db_user']) . "');\n";
    $content .= "define('DB_PASS', '" . addslashes($_SESSION['db_pass']) . "');\n";

    return $content;
}

function die_gracefully($title, $message) {
    render_page($title, function() use ($message) {
        echo '<h2>An Error Occurred</h2>';
        echo '<p class="error">' . htmlspecialchars($message) . '</p>';
        echo '<a href="?step=1" class="button">Try Again</a>';
    });
    exit;
}

function render_page($title, $content_callback) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-g">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ignition Arcade Installer - <?php echo htmlspecialchars($title); ?></title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; line-height: 1.6; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 50px auto; background-color: #fff; padding: 20px 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
            h1, h2 { color: #1A1A2E; }
            ul { list-style: none; padding-left: 0; }
            li { padding: 5px 0; border-bottom: 1px solid #eee; }
            .success { color: #28a745; font-weight: bold; }
            .error { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; }
            .button, button { display: inline-block; background-color: #007bff; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; border: none; font-size: 16px; cursor: pointer; }
            .button-disabled { background-color: #ccc; cursor: not-allowed; }
            form { margin-top: 20px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input[type="text"], input[type="password"], input[type="email"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Ignition Arcade Installer</h1>
            <hr>
            <?php $content_callback(); ?>
        </div>
    </body>
    </html>
    <?php
}
?>