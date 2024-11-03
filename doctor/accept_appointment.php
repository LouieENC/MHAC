<?php 
// accept_appointment.php
session_start();
include '../includes/db_connect.php';

// Check if session and form data are set
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['appointment_id'], $_POST['date'], $_POST['time'])) {
    $appointment_id = $_POST['appointment_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $appointment_datetime = "$date $time";

    // Prepare and execute the statement to update appointment status
    $stmt = $conn->prepare("UPDATE Appointments SET status = 'Confirmed', appointment_date = ? WHERE appointment_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $appointment_datetime, $appointment_id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    // Retrieve the student ID related to this appointment
    $student_id_query = $conn->prepare("SELECT student_id FROM Appointments WHERE appointment_id = ?");
    if ($student_id_query) {
        $student_id_query->bind_param("i", $appointment_id);
        $student_id_query->execute();
        $student_id_result = $student_id_query->get_result();
        $student_id = $student_id_result->fetch_assoc()['student_id'];
        $student_id_query->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    // Retrieve the user_id associated with this student from the Users table
    $user_id_query = $conn->prepare("SELECT user_id FROM Users WHERE student_id = ?");
    if ($user_id_query) {
        $user_id_query->bind_param("i", $student_id);
        $user_id_query->execute();
        $user_id_result = $user_id_query->get_result();
        $user_data = $user_id_result->fetch_assoc();
        $user_id_query->close();

        // If the student has an associated user_id, proceed with sending the notification
        if ($user_data) {
            $user_id = $user_data['user_id'];
            $message = "Your appointment has been confirmed for $appointment_datetime.";

            $stmt2 = $conn->prepare("INSERT INTO Notifications (user_id, message, type) VALUES (?, ?, 'Appointment')");
            if ($stmt2) {
                $stmt2->bind_param("is", $user_id, $message);
                $stmt2->execute();
                $stmt2->close();
            } else {
                die("Error preparing statement: " . $conn->error);
            }

            // Redirect to the doctor dashboard with a success message
            header("Location: doctor_dashboard.php?message=Appointment Accepted");
            exit();
        } else {
            die("Error: No user found for the specified student ID.");
        }
    } else {
        die("Error preparing statement: " . $conn->error);
    }
} else {
    header("Location: doctor_dashboard.php?error=Missing appointment details");
    exit();
}
?>
