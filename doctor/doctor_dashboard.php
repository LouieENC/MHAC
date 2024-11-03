<?php
    //doctor_dashboard.php
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
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Doctor') {
        header("Location: ../access_denied.php");
        exit();
    }

    include '../includes/db_connect.php';

    // Query to count total active patients
    $patientsQuery = "
        SELECT COUNT(*) as total_patients 
        FROM Users 
        JOIN Students ON Users.student_id = Students.student_id 
        WHERE Users.role_id = 1 AND Students.status = 'Active'
    ";
    $patientsResult = $conn->query($patientsQuery);
    $patientsCount = $patientsResult->fetch_assoc()['total_patients'];

    // Query to count total doctors
    $doctorsQuery = "SELECT COUNT(*) as total_doctors FROM Doctors";
    $doctorsResult = $conn->query($doctorsQuery);
    $doctorsCount = $doctorsResult->fetch_assoc()['total_doctors'];

    $doctor_id = $_SESSION['doctor_id'];

    // Fetch confirmed appointments along with student names
    $confirmedAppointments = $conn->query("
        SELECT Appointments.*, Students.name AS student_name
        FROM Appointments
        JOIN Students ON Appointments.student_id = Students.student_id
        WHERE Appointments.doctor_id = $doctor_id AND Appointments.status = 'Confirmed'
    ");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - MUST Clinic Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #notifications {
            width: 100%;
            max-width: 600px;
            max-height: 210px;
            overflow-x: auto;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        #notifications h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
            position: sticky;
        }

        .notification {
            background-color: #ffffff;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification p {
            font-size: 1em;
            color: #555;
            margin-bottom: 10px;
        }

        .notification form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .notification label {
            font-size: 0.9em;
            color: #666;
        }

        .notification input[type="date"],
        .notification input[type="time"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            color: #333;
        }

        .notification button[type="submit"] {
            padding: 10px;
            font-size: 1em;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .notification button[type="submit"]:hover {
            background-color: #45a049;
        }

        .join-btn {
            background-color: #ccc;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #fff;
            cursor: not-allowed;
            transition: background-color 0.3s ease;
        }

        .join-btn.enabled {
            background-color: #4CAF50;
            cursor: pointer;
        }

        .join-btn.enabled:hover {
            background-color: #45a049;
        }


    </style>
</head>
<body>
    <!-- Burger Menu Icon for Mobile -->
    <button class="burger"><i class="fas fa-bars"></i></button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <div class="dashboard-container">
        <!-- Side Navigation -->
        <nav class="sidebar" id="sidebar">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-list" aria-label="Sidebar Navigation">
                <li><a href="doctor_dashboard.php" aria-label="Dashboard"><i class="fas fa-tachometer-alt"></i><span class="nav-text">Dashboard</span></a></li>
                <li><a href="patients.php" aria-label="Manage Patients"><i class="fas fa-user-injured"></i><span class="nav-text">Patients</span></a></li>
                <li><a href="../includes/logout.php" aria-label="Logout"><i class="fas fa-sign-out-alt"></i> <span class="nav-text">Logout</span></a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
            <header class="header">
                <h1>Doctor Dashboard</h1>
            </header>
            <section class="content">
                <div class="overview">
                    <div class="card">
                        <h3>Total Patients</h3>
                        <p><?php echo $patientsCount; ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Doctors</h3>
                        <p><?php echo $doctorsCount; ?></p>
                    </div>
                    <div class="card">
                        <h3>Health Tips Published</h3>
                        <p>16</p>
                    </div>
                </div>

                <?php 
                    $doctor_id = $_SESSION['doctor_id'];

                    // Fetch pending appointments along with student names
                    $pendingAppointments = $conn->query("
                        SELECT Appointments.*, Students.name AS student_name
                        FROM Appointments
                        JOIN Students ON Appointments.student_id = Students.student_id
                        WHERE Appointments.doctor_id = $doctor_id AND Appointments.status = 'Pending'
                    ");
                ?>

                <div class="analytics-section">
                    <div class="analytics-card" id="notifications">
                        <h2>Appointment Requests</h2>
                        <?php while($appointment = $pendingAppointments->fetch_assoc()): ?>
                            <div class="notification">
                                <p>Request from <?= htmlspecialchars($appointment['student_name']) ?> (Student ID: <?= $appointment['student_id'] ?>)</p>
                                <form action="accept_appointment.php" method="POST">
                                    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                    <label for="date">Select Date:</label>
                                    <input type="date" name="date" required>
                                    <label for="time">Select Time:</label>
                                    <input type="time" name="time" required>
                                    <button type="submit">Accept</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="analytics-card" id="notifications">
                        <h2>Confirmed Appointments</h2>
                        <?php while($appointment = $confirmedAppointments->fetch_assoc()): ?>
                            <div class="notification" data-appointment-time="<?= $appointment['appointment_date'] ?>">
                                <p>Appointment with <?= htmlspecialchars($appointment['student_name']) ?> (Student ID: <?= $appointment['student_id'] ?>) on <?= $appointment['appointment_date'] ?></p>
                                <button class="join-btn" disabled>Join</button>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            </section>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Function to check appointment times and enable "Join" buttons
        function enableJoinButtons() {
            const notifications = document.querySelectorAll(".notification");
            const currentTime = new Date();

            notifications.forEach(notification => {
                const appointmentTime = new Date(notification.getAttribute("data-appointment-time"));
                const joinButton = notification.querySelector(".join-btn");

                // Enable the button if the current time has passed or is equal to the appointment time
                if (currentTime >= appointmentTime) {
                    joinButton.disabled = false;
                    joinButton.classList.add("enabled");
                    joinButton.addEventListener("click", () => {
                        window.location.href = '../group-video-chat-master/index.php'; 
                    });
                }
            });
        }

        // Check appointment times on page load and every minute after
        document.addEventListener("DOMContentLoaded", enableJoinButtons);
        setInterval(enableJoinButtons, 60000); // Run every 60 seconds


    </script>

</body>
</html>
