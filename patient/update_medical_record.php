<?php
include "../includes/db_connect.php";
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login-signup.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$weight = $_POST['weight'];
$height = $_POST['height'];
$blood_group = $_POST['blood_group'];
$chronic_conditions = $_POST['chronic_conditions'];
$allergies = $_POST['allergies'];

// Check if a record already exists for this student
$query_check = "SELECT 1 FROM MedicalRecords WHERE student_id = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("i", $student_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Record exists, so update
    $query_update = "UPDATE MedicalRecords SET weight=?, height=?, blood_group=?, chronic_conditions=?, allergies=? WHERE student_id=?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("ddsssi", $weight, $height, $blood_group, $chronic_conditions, $allergies, $student_id);

    if ($stmt_update->execute()) {
        header("Location: student_profile.php?message=Medical record updated successfully");
        exit();
    } else {
        echo "<p>Error updating record: " . $conn->error . "</p>";
    }
} else {
    // No record exists, so insert
    $query_insert = "INSERT INTO MedicalRecords (student_id, weight, height, blood_group, chronic_conditions, allergies) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param("iddsss", $student_id, $weight, $height, $blood_group, $chronic_conditions, $allergies);

    if ($stmt_insert->execute()) {
        header("Location: student_profile.php?message=Medical record added successfully");
        exit();
    } else {
        echo "<p>Error inserting record: " . $conn->error . "</p>";
    }
}
?>
