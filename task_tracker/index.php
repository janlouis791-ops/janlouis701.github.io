<?php
session_start();
require 'db.php';

// If already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Query the database for the user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Login successful: Store data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            
            // PRG Pattern: Redirect so browser last action is GET, not POST
            // This prevents "Confirm Form Resubmission" on back button
            header("Location: dashboard.php");
            exit;
        } else {
            // Store error in session and redirect back to login (GET)
            $_SESSION['login_error'] = 'Invalid email or password.';
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'Please fill in all fields.';
        header("Location: index.php");
        exit;
    }
}

// Retrieve and clear error from session
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker - Login</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        // Block back button on login page — if user is logged out, trap them here
        history.pushState(null, null, location.href);
        window.addEventListener("popstate", function() {
            history.pushState(null, null, location.href);
        });
        // Also force reload if loaded from bfcache (back button cache)
        window.addEventListener("pageshow", function(e) {
            if (e.persisted) window.location.reload();
        });
    </script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <img src="login-bg.png" alt="Login Illustration" style="object-fit: contain; width: 100%; height: 100%; max-height: 400px;">
        </div>
        <div class="auth-right">
            <h2>Welcome</h2>
            
            <!-- Display Error Message -->
            <?php if ($error): ?>
                <div style="color: #ea4335; background: #fce8e6; padding: 10px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #fad2cf;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                <div class="auth-actions">
                    <button type="submit" class="btn-primary">Login</button>
                    <div class="switch-auth">
                        Don't have an account? <a href="register.php">Sign up</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
