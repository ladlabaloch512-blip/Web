<?php
session_start();

// If the user is not logged in as an admin, redirect to the login page.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the config file exists. If not, the script hasn't been installed.
if (!file_exists('../app/config.php')) {
    die("Configuration file not found. Please run the installer first.");
}

require_once '../app/config.php';

// --- Page-specific logic will go here ---
$page_title = "Admin Dashboard";


// --- View ---
include 'partials/header.php';
?>

<div class="content">
    <h2>Welcome to the Ignition Arcade Admin Panel!</h2>
    <p>This is your dashboard. From here, you can manage games, users, settings, and more.</p>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Games</h3>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h3>Categories</h3>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h3>Pending Tasks</h3>
            <p>0</p>
        </div>
    </div>

</div>

<?php
include 'partials/footer.php';
?>