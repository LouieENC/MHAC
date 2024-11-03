<?php
    require '../includes/db_connect.php';

    if (isset($_GET['id'])) {
        $doctor_id = $_GET['id'];
        $query = "SELECT * FROM doctors WHERE doctor_id = $doctor_id";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode([]);
        }
    }
?>
