<?php
// fetch_medical_record.php
include "../includes/db_connect.php";
session_start();

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $query = "SELECT * FROM MedicalRecords WHERE student_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No records found."]);
    }
}

?>
