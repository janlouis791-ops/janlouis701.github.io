<?php
session_start();
require 'db.php';

// If already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($fullname) && !empty($email) && !empty($mobile) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $_SESSION['reg_error'] = 'Passwords do not match.';
            header("Location: register.php");
            exit;
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['reg_error'] = 'Email is already registered.';
                header("Location: register.php");
                exit;
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $pdo->prepare("INSERT INTO users (fullname, email, mobile, password) VALUES (?, ?, ?, ?)");
                if ($insertStmt->execute([$fullname, $email, $mobile, $hashed_password])) {
                    $_SESSION['reg_success'] = 'Account created successfully! You can now <a href="index.php" style="color:#1a73e8; font-weight:bold;">Log in</a>.';
                    header("Location: register.php");
                    exit;
                } else {
                    $_SESSION['reg_error'] = 'Failed to create account. Please try again.';
                    header("Location: register.php");
                    exit;
                }
            }
        }
    } else {
        $_SESSION['reg_error'] = 'Please fill in all fields.';
        header("Location: register.php");
        exit;
    }
}

// Retrieve and clear messages from session
if (isset($_SESSION['reg_error'])) {
    $error = $_SESSION['reg_error'];
    unset($_SESSION['reg_error']);
}
if (isset($_SESSION['reg_success'])) {
    $success = $_SESSION['reg_success'];
    unset($_SESSION['reg_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker - Create Account</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <img src="login-bg.png" alt="Register Illustration" style="object-fit: contain; width: 100%; height: 100%; max-height: 400px;">
        </div>
        <div class="auth-right">
            <h2>Create account</h2>
            
            <?php if ($error): ?>
                <div style="color: #ea4335; background: #fce8e6; padding: 10px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #fad2cf;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="color: #34a853; background: #e6f4ea; padding: 10px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #ceead6;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="input-group">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="text" name="mobile" placeholder="Mobile Number" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn-primary">Sign up</button>
                    <div class="switch-auth" style="text-align: right; width: 100%; margin-top: 10px;">
                        <a href="index.php" style="color: #888; font-size: 12px; font-weight:bold; text-transform:uppercase;">Back to Login</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
