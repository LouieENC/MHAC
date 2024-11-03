<?php
    // schedule_appointment.php
    session_start();
    include '../includes/db_connect.php';

    if ($_SESSION['role'] == 'Student') {
        $student_id = $_SESSION['student_id'];
        $doctor_id = $_POST['doctor_id'];

        // Insert appointment request with a default status of 'Pending'
        $stmt = $conn->prepare("INSERT INTO Appointments (student_id, doctor_id, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("ii", $student_id, $doctor_id);
        $stmt->execute();

        // Retrieve the user_id of the doctor from the Users table for notification
        $userQuery = $conn->prepare("SELECT user_id FROM Users WHERE doctor_id = ?");
        $userQuery->bind_param("i", $doctor_id);
        $userQuery->execute();
        $userQuery->bind_result($doctor_user_id);
        $userQuery->fetch();
        $userQuery->close();

        // Insert notification for the doctor
        if ($doctor_user_id) {
            $message = "New appointment request from Student ID: $student_id.";
            $stmt2 = $conn->prepare("INSERT INTO Notifications (user_id, message, type) VALUES (?, ?, 'Appointment')");
            $stmt2->bind_param("is", $doctor_user_id, $message);
            $stmt2->execute();
            $stmt2->close();
        }

        // Redirect to student dashboard with success message
        header("Location: student_dashboard.php?success=1");
        exit();
    }
?>

