<?php
    session_start();

    include '../includes/db_connect.php';

    // Check if the success parameter is set in the URL
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo "<script>alert('Appointment requested successfully!');</script>";
    }

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
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
        header("Location: ../access_denied.php");
        exit();
    }

    $student_id = $_SESSION['student_id'];

    // Retrieve student name
    $studentQuery = $conn->query("SELECT name FROM Students WHERE student_id = $student_id");
    $studentName = $studentQuery->fetch_assoc()['name'] ?? 'Student';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/user-page-styles.css">
    <link rel="stylesheet" href="../assets/css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Student Dashboard - MUST Clinic Online</title>
    <style>
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select {
            width: 20%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Welcome, <?php echo htmlspecialchars($studentName); ?></h1>
            <p>Your personalized space for managing your health</p>
        </div>
    </header>

    
    <!-- Sidebar containing nav -->
    <nav class="top-nav">
        <ul>
            <li><a href="#" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#telemedicine-consultation"><i class="fas fa-video"></i> Consultation</a></li>
            <li><a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>   

    <!-- Begining of container -->
    <div class="container">
        <main>
            <!-- Symptom Checker Card -->
            <div class="box" id="symptom-checker">
                <h2>Symptom Checker</h2>
                <p>Use our symptom checker to get insights into your health.</p>
                <a class="card-btn" href="../symptom_checker/symptom-checker.php">Check Symptoms</a>
            </div>

            <!-- Telemedicine Consultation Card -->
            <div class="box" id="telemedicine-consultation">
                <h2>Telemedicine Consultation</h2>
                <p>Want to book a virtual appointment with a doctor?</p>
                <form action="schedule_appointment.php" method="POST">
                    <label for="doctor_id">Choose Doctor:</label>
                    <select name="doctor_id" required>
                        <?php
                        // Query to get all doctors from the database
                        $doctorsQuery = $conn->query("SELECT doctor_id, name, specialization FROM Doctors");
                        while ($doctor = $doctorsQuery->fetch_assoc()) {
                            echo "<option value='{$doctor['doctor_id']}'>{$doctor['specialization']}</option>";
                        }
                        ?>
                    </select>
                    <br>
                    <button class="card-btn" type="submit">Request Appointment</button>
                </form>
            </div>


            <!-- Notifications Section -->
            <div class="notifications-section" id="notificationsPanel">
                <div class="notifications-header">
                    <h2>Notifications</h2>
                    <button class="close-btn" onclick="closeNotifications()">×</button>
                </div>
                <?php
                    $notifications = $conn->query("SELECT * FROM Appointments WHERE student_id = $student_id AND status = 'Confirmed'");
                    
                    if ($notifications->num_rows > 0) {
                        while ($notification = $notifications->fetch_assoc()) {
                            $appointmentTime = $notification['appointment_date']; // Format: Y-m-d H:i:s
                            echo "<div class='notification' data-appointment-time='$appointmentTime'>";
                            echo "<p>Appointment confirmed with Doctor ID {$notification['doctor_id']} on {$notification['appointment_date']}</p>";
                            echo "<button class='join-btn' disabled>Join</button>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='no-notifications'>No notifications at this time.</p>";
                    }
                ?>
            </div>



        </main>
    </div> 
    <!-- End of container -->

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy-policy">Privacy Policy</a>
                <a href="#terms-of-service">Terms of Service</a>
                <a href="#contact-us">Contact Us</a>
            </div>
            <br>
            <p>&copy; 2024 MUST Online Clinic. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="../assets/js/user-page-scripts.js"></script>
    <script>
        function closeNotifications() {
            document.getElementById("notificationsPanel").style.display = "none";
        }

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
