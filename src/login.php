<?php
session_start();
require 'db.php';
$error_message = '';

if (isset($_SESSION["user_id"])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hash, $is_admin);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION["user_id"] = $user_id;
                
                if ($is_admin) {
                    $_SESSION['is_admin'] = true;
                    header("Location: admin_dashboard.php");
                } else {
                    $_SESSION['is_admin'] = false;
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MiLog</title>
    <style>
        :root {
            --primary-color: #2563eb; --primary-hover: #1d4ed8; --bg-color: #f3f4f6;
            --card-bg: #ffffff; --text-color: #1f2937; --secondary-text: #6b7280;
            --error-color: #dc2626;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg-color); color: var(--text-color);
            display: flex; align-items: center; justify-content: center; min-height: 100vh;
        }
        .auth-container { width: 100%; max-width: 400px; padding: 2rem; }
        .auth-card {
            background-color: var(--card-bg); padding: 2.5rem;
            border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .auth-title { font-size: 1.875rem; font-weight: 600; text-align: center; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--secondary-text); }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem; }
        .btn {
            width: 100%; display: inline-block; padding: 0.75rem 1.5rem;
            background-color: var(--primary-color); color: white; text-decoration: none;
            border-radius: 0.375rem; border: none; cursor: pointer; font-size: 1rem;
            transition: background-color 0.15s ease;
        }
        .btn:hover { background-color: var(--primary-hover); }
        .auth-switch-link { text-align: center; margin-top: 1.5rem; color: var(--secondary-text); }
        .auth-switch-link a { color: var(--primary-color); text-decoration: none; }
        .error-message {
            color: var(--error-color); background-color: #fee2e2;
            padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">MiLog</h1>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn">Log In</button>
            </form>
            <p class="auth-switch-link">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </div>
    </div>
</body>
</html>