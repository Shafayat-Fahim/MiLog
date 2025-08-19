<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'MiLog' ?></title>
    <style>
        :root {
            --primary-color: #2563eb; --primary-hover: #1d4ed8; --bg-color: #f3f4f6;
            --card-bg: #ffffff; --text-color: #1f2937; --secondary-text: #6b7280;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-color); color: var(--text-color);
            line-height: 1.6; padding-top: 80px;
        }
        .navbar {
            background-color: var(--card-bg); padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); position: fixed;
            top: 0; left: 0; right: 0; z-index: 1000;
        }
        .navbar-container {
            max-width: 1200px; margin: 0 auto; display: flex;
            justify-content: space-between; align-items: center;
        }
        .navbar-brand { font-size: 1.5rem; font-weight: 600; color: var(--text-color); text-decoration: none; }
        .navbar-nav { display: flex; gap: 1.5rem; align-items: center; list-style: none; }
        .nav-link { color: var(--secondary-text); text-decoration: none; font-weight: 500; transition: color 0.15s ease; }
        .nav-link:hover { color: var(--primary-color); }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .welcome-section { background-color: var(--card-bg); padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .card { background-color: var(--card-bg); padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn {
            display: inline-block; padding: 0.75rem 1.5rem; background-color: var(--primary-color);
            color: white; text-decoration: none; border-radius: 0.375rem;
            transition: background-color 0.15s ease; text-align: center;
        }
        .btn:hover { background-color: var(--primary-hover); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="<?= (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'admin_dashboard.php' : 'dashboard.php' ?>" class="navbar-brand">MiLog</a>
            
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="admin_dashboard.php" class="nav-link">Password Reset</a></li>
                    <li><a href="logout.php" class="nav-link">Sign Out</a></li>
                <?php else: ?>
                    <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                    <li><a href="vehicles.php" class="nav-link">My Vehicles</a></li>
                    <li><a href="logout.php" class="nav-link">Sign Out</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>