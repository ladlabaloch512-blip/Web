<?php
// Installer for PubAd
// This script guides the user through the installation process.

// --- Configuration ---
$required_php_version = '7.4.0';
$required_extensions = ['mysqli'];
$config_path = '../app/config.php';
$config_dir = '../app/';

// --- State Management ---
// Determine the current step, default to 1 if not set
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// --- Requirement Checks ---
$php_version_ok = version_compare(PHP_VERSION, $required_php_version, '>=');
$extensions_ok = true;
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $extensions_ok = false;
        break;
    }
}
$config_dir_writable = is_writable($config_dir);
$all_requirements_met = $php_version_ok && $extensions_ok && $config_dir_writable;

// --- Logic to prevent re-installation ---
if (file_exists($config_path)) {
    $step = 'complete';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PubAd Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .installer-container { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; padding: 40px; }
        h1 { color: #1a202c; text-align: center; margin-bottom: 30px; }
        .step-content { margin-top: 20px; }
        .btn { display: inline-block; background-color: #4A90E2; color: #fff; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; text-align: center; transition: background-color 0.3s; }
        .btn:hover { background-color: #357ABD; }
        .btn-disabled { background-color: #a0aec0; cursor: not-allowed; }
        .requirements-list { list-style-type: none; padding: 0; margin: 20px 0; }
        .requirements-list li { background-color: #f7fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 5px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .status { font-weight: 700; }
        .status-ok { color: #48bb78; }
        .status-fail { color: #f56565; }
        .text-center { text-align: center; }
        .error-message { background-color: #fed7d7; border: 1px solid #f56565; color: #c53030; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .success-message { background-color: #c6f6d5; border: 1px solid #68d391; color: #2f855a; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 1rem; }
        code { background-color: #e2e8f0; padding: 2px 4px; border-radius: 3px; font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace; }
    </style>
</head>
<body>
    <div class="installer-container">
        <h1>PubAd Installer</h1>

        <?php if ($step === 1): ?>
            <!-- Step 1: Welcome and Requirements Check -->
            <div class="step-content">
                <h2>Step 1: Server Requirements</h2>
                <p>Welcome to PubAd! Before we begin, we need to check if your server meets the minimum requirements.</p>

                <ul class="requirements-list">
                    <li>PHP Version (>= <?php echo $required_php_version; ?>)
                        <span class="status <?php echo $php_version_ok ? 'status-ok' : 'status-fail'; ?>">
                            <?php echo $php_version_ok ? 'OK' : 'FAIL'; ?> (Your version: <?php echo PHP_VERSION; ?>)
                        </span>
                    </li>
                    <?php foreach ($required_extensions as $ext): ?>
                    <li>PHP Extension: <?php echo $ext; ?>
                        <span class="status <?php echo extension_loaded($ext) ? 'status-ok' : 'status-fail'; ?>">
                            <?php echo extension_loaded($ext) ? 'OK' : 'FAIL'; ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                    <li>Config Directory Writable (<?php echo $config_dir; ?>)
                        <span class="status <?php echo $config_dir_writable ? 'status-ok' : 'status-fail'; ?>">
                            <?php echo $config_dir_writable ? 'OK' : 'FAIL'; ?>
                        </span>
                    </li>
                </ul>

                <div class="text-center">
                    <?php if ($all_requirements_met): ?>
                        <a href="?step=2" class="btn">Start Installation</a>
                    <?php else: ?>
                        <p class="error-message">Please fix the failed requirements before proceeding.</p>
                        <a href="?step=1" class="btn btn-disabled" onclick="event.preventDefault();">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($step === 2): ?>
            <!-- Step 2: Database Configuration -->
            <div class="step-content">
                <h2>Step 2: Database Configuration</h2>
                <p>Please provide your database connection details. These will be saved in <code>app/config.php</code>.</p>

                <form action="install.php?step=2" method="POST">
                    <div class="form-group">
                        <label for="db_host">Database Host</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    <div class="form-group">
                        <label for="db_name">Database Name</label>
                        <input type="text" id="db_name" name="db_name" required>
                    </div>
                    <div class="form-group">
                        <label for="db_user">Database User</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn">Test Connection & Next</button>
                    </div>
                </form>
            </div>
        <?php elseif ($step === 3): ?>
            <!-- Step 3: Admin Account Setup -->
            <div class="step-content">
                <h2>Step 3: Admin Account Setup</h2>
                <p>Create your administrator account.</p>

                <form action="install.php?step=3" method="POST">
                    <div class="form-group">
                        <label for="admin_user">Admin Username</label>
                        <input type="text" id="admin_user" name="admin_user" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" id="admin_email" name="admin_email" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_pass">Admin Password</label>
                        <input type="password" id="admin_pass" name="admin_pass" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn">Finish Installation</button>
                    </div>
                </form>
            </div>
        <?php elseif ($step === 'complete'): ?>
            <!-- Already Installed Message -->
            <div class="step-content">
                <h2>Installation Complete</h2>
                <div class="success-message">
                    PubAd appears to be already installed. For security, please <strong>delete the /installer directory</strong> from your server.
                </div>
                <div class="text-center" style="margin-top: 20px;">
                    <a href="../index.php" class="btn">Go to Homepage</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
