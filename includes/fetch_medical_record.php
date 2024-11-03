<?php
include '../includes/db_connect.php';

if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);

    // Query to get the medical record
    $query = "SELECT * FROM MedicalRecords WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        echo "<p><strong>Weight:</strong> {$record['weight']} kg</p>";
        echo "<p><strong>Height:</strong> {$record['height']} cm</p>";
        echo "<p><strong>Blood Group:</strong> {$record['blood_group']}</p>";
        echo "<p><strong>Chronic Conditions:</strong> {$record['chronic_conditions']}</p>";
        echo "<p><strong>Allergies:</strong> {$record['allergies']}</p>";
        echo "<p><strong>Record Date:</strong> {$record['created_at']}</p>";
    } else {
        echo "<p>No medical record found for this student.</p>";
    }
    $stmt->close();
}
$conn->close();
?>
