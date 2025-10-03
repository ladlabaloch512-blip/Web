<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> - Ignition Arcade</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Ignition Arcade</h2>
            </div>
            <ul class="nav-menu">
                <li class="active"><a href="index.php">Dashboard</a></li>
                <li><a href="#">Game Management</a></li>
                <li><a href="#">User Management</a></li>
                <li><a href="#">Categories & Tags</a></li>
                <li><a href="#">Ad Management</a></li>
                <li><a href="#">Page Management</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </div>
        <div class="main-content">
            <header class="topbar">
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="logout.php">Logout</a>
                </div>
            </header>
            <main class="content-area">
                <!-- Content of the specific page will be loaded here -->