<?php
     session_start();

    include '../includes/db_connect.php';


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

    // Query to count total patients (users who are students)
    $patients_sql = "SELECT COUNT(*) AS total_patients FROM Users JOIN Students ON Users.student_id = Students.student_id";
    $patients_result = $conn->query($patients_sql);
    $total_patients = $patients_result->fetch_assoc()['total_patients'];

    // Query to count total doctors (users who are doctors)
    $doctors_sql = "SELECT COUNT(*) AS total_doctors FROM Users JOIN Doctors ON Users.doctor_id = Doctors.doctor_id";
    $doctors_result = $conn->query($doctors_sql);
    $total_doctors = $doctors_result->fetch_assoc()['total_doctors'];

    // Query to count total health tips
    $health_tips_sql = "SELECT COUNT(*) AS health_tips FROM Conditions";
    $health_tips_result = $conn->query($health_tips_sql);
    $total_health_tips = $health_tips_result->fetch_assoc()['health_tips'];


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
    <title>Admin Dashboard - Mobile Health System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Burger Menu Icon for Mobile -->
    <button class="burger"><i class="fas fa-bars"></i></button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <div class="dashboard-container">
        <!-- Side Navigation -->
        <?php include '../includes/nav.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <h1>Admin Dashboard</h1>
            </header>
            <section class="content">
                <div class="overview">
                    <div class="card">
                        <h3>Total Patients</h3>
                        <p><?php echo $total_patients; ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Doctors</h3>
                        <p><?php echo $total_doctors; ?></p>
                    </div>
                    <div class="card">
                        <h3>Health Tips Published</h3>
                        <p><?php echo $total_health_tips; ?></p>
                    </div>
                </div>

                <div class="analytics-section">
                    <div class="analytics-card">
                        <h3>Consultations Analytics</h3>
                        <p>Graph showing the number of successful consultations over time.</p>
                    </div>
                    <div class="analytics-card">
                        <h3>Patient Registration Trends</h3>
                        <p>Graph showing patient registrations over the past months.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // JavaScript to toggle sidebar and overlay
        document.addEventListener("DOMContentLoaded", function () {
            const burger = document.querySelector('.burger');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            // Toggle sidebar and overlay
            burger.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });

            // Hide sidebar and overlay when overlay is clicked
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        });

    </script>
</body>


</html>
