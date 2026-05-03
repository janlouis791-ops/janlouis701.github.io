<?php
session_start();
require_once 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Prevent browser from caching authenticated pages
// This ensures the back button won't show dashboard after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker</title>
    <!-- Prevent browser from caching this page -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Block back button on dashboard — trap user here while logged in
        history.pushState(null, null, location.href);
        window.addEventListener("popstate", function() {
            history.pushState(null, null, location.href);
        });
        // Force reload if loaded from bfcache (back button cache)
        window.addEventListener("pageshow", function(e) {
            if (e.persisted) window.location.reload();
        });
    </script>
</head>
<body class="app-body">
    <div class="app-container">
        <div class="header">
            <div class="header-title">
                <h1><i class="fa fa-check-square"></i> Task Tracker</h1>
                <p>Smart task management for students</p>
            </div>
            <div style="display:flex; align-items:center; gap:15px;">
                <span style="color:white; font-size:14px; font-weight:500;">
                    <i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['fullname']) ?>
                </span>
                <a href="logout.php" style="background:rgba(255,255,255,0.2); color:white; padding:8px 18px; border-radius:20px; text-decoration:none; font-size:13px; font-weight:600; border:1px solid rgba(255,255,255,0.4);">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        <div class="nav-bar">
            <a href="dashboard.php" class="nav-item <?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa fa-home"></i> Dashboard</a>
        </div>
