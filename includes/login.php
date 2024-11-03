<?php
// login.php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($conn) {
        // First check if the email exists in the Admins table
        $stmt = $conn->prepare("SELECT a.*, r.role_name FROM Admins a 
                                JOIN Roles r ON a.role_id = r.role_id 
                                WHERE a.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $admin_result = $stmt->get_result();

        // If the user is an admin
        if ($admin_result && $admin_result->num_rows > 0) {
            $admin_row = $admin_result->fetch_assoc();
            $hashed_password = $admin_row['password'];
            $role_name = $admin_row['role_name'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Set session variables for admin
                $_SESSION['logged_in'] = true;
                $_SESSION['admin_id'] = $admin_row['admin_id'];
                $_SESSION['role'] = $role_name;
                $_SESSION['name'] = $admin_row['name'];
                $_SESSION['last_activity'] = time();

                // Redirect to admin dashboard
                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header("Location: ../login-signup.php");
                exit();
            }
        }

        // If not an admin, check the Users table for Students/Doctors
        $stmt = $conn->prepare("SELECT u.*, r.role_name FROM Users u 
                                JOIN Roles r ON u.role_id = r.role_id 
                                WHERE u.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user_result = $stmt->get_result();

        if ($user_result && $user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $hashed_password = $user_row['password'];
            $role_name = $user_row['role_name'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Set session variables for users
                $_SESSION['logged_in'] = true;
                if ($role_name == 'Student') {
                    $_SESSION['student_id'] = $user_row['student_id'];  // Store student_id in session
                } elseif ($role_name == 'Doctor') {
                    $_SESSION['doctor_id'] = $user_row['doctor_id'];  // Store doctor_id in session
                }
                $_SESSION['role'] = $role_name;
                $_SESSION['name'] = $user_row['name'];
                $_SESSION['last_activity'] = time();

                // Redirect based on role
                if ($role_name == 'Student') {
                    header("Location: ../patient/student_dashboard.php");
                } elseif ($role_name == 'Doctor') {
                    header("Location: ../doctor/doctor_dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
                header("Location: ../login-signup.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "User not found!";
            header("Location: ../login-signup.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Database connection failed!";
        header("Location: ../login-signup.php");
        exit();
    }
}
?>
