<?php
//register.php

session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password

    // Check if the user already exists in the Users table
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_user = $stmt->get_result();

    if ($result_user->num_rows > 0) {
        // Email already exists in the Users table
        $_SESSION['register_error'] = "User already exists!";
    } else {
        // Check if email exists in Students table
        $stmt = $conn->prepare("SELECT student_id FROM Students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_student = $stmt->get_result();

        // Check if email exists in Doctors table
        $stmt = $conn->prepare("SELECT doctor_id FROM Doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_doctor = $stmt->get_result();

        if ($result_student->num_rows > 0) {
            // Email found in Students table, assign Student role
            $row_student = $result_student->fetch_assoc();
            $student_id = $row_student['student_id'];

            // Get the role_id for Student from the Roles table
            $stmt = $conn->prepare("SELECT role_id FROM Roles WHERE role_name = 'Student'");
            $stmt->execute();
            $result_role = $stmt->get_result();
            $row_role = $result_role->fetch_assoc();
            $role_id = $row_role['role_id'];

            // Insert into Users table with Student role
            $insert_stmt = $conn->prepare("INSERT INTO Users (student_id, email, password, role_id) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("issi", $student_id, $email, $password, $role_id);

            if ($insert_stmt->execute()) {
                $_SESSION['register_success'] = "Registration successful!";
            } else {
                $_SESSION['register_error'] = "Error: " . $insert_stmt->error;
            }

        } elseif ($result_doctor->num_rows > 0) {
            // Email found in Doctors table, assign Doctor role
            $row_doctor = $result_doctor->fetch_assoc();
            $doctor_id = $row_doctor['doctor_id'];

            // Get the role_id for Doctor from the Roles table
            $stmt = $conn->prepare("SELECT role_id FROM Roles WHERE role_name = 'Doctor'");
            $stmt->execute();
            $result_role = $stmt->get_result();
            $row_role = $result_role->fetch_assoc();
            $role_id = $row_role['role_id'];

            // Insert into Users table with Doctor role
            $insert_stmt = $conn->prepare("INSERT INTO Users (doctor_id, email, password, role_id) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("issi", $doctor_id, $email, $password, $role_id);

            if ($insert_stmt->execute()) {
                $_SESSION['register_success'] = "Registration successful!";
            } else {
                $_SESSION['register_error'] = "Error: " . $insert_stmt->error;
            }

        } else {
            // Email not found in either Students or Doctors table
            $_SESSION['register_error'] = "Email not found in the system!";
        }
    }

    // Redirect back to the form
    header("Location: ../login-signup.php");
    exit();
}

$conn->close();
?>
