<?php
session_start();

// Set session timeout duration (30 minutes = 1800 seconds)
$timeout_duration = 1800;

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check for session expiration (last activity)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session has expired, destroy session and redirect to login
    session_unset();     // unset $_SESSION variables
    session_destroy();   // destroy the session
    header("Location: login.php?message=Session expired. Please log in again.");
    exit();
}

// Update last activity time stamp
$_SESSION['last_activity'] = time();

// Check if the user is a student
if ($_SESSION['role'] !== 'Student') {
    // Redirect to access denied page if user is not a student
    header("Location: access_denied.php");
    exit();
}
?>
