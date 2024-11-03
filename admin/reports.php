<?php
     session_start();

    $timeout_duration = 1800;

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ../login-signup.php");
        exit();
    }

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../login-signup.php?message=Session expired. Please log in again.");
        exit();
    }

    $_SESSION['last_activity'] = time();

    // Check user role
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
        header("Location: ../access_denied.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - MUST Clinic Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Side Navigation -->
        <?php include '../includes/nav.php'; ?>
        <!-- Main Content -->
        <div class="main-content">
          <header class="header">
              <h1>Manage Reports</h1>
          </header>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
