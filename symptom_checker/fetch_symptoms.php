<?php
    // fetch_symptom.php
    require '../includes/db_connect.php';

    header('Content-Type: application/json'); // Response format: JSON

    $symptoms = [];

    if (isset($_GET['q'])) {
        $query = $conn->real_escape_string($_GET['q']);
        $sql = "SELECT name FROM Symptoms WHERE name LIKE '%$query%' ORDER BY CHAR_LENGTH(name) ASC LIMIT 10"; // Fuzzy matching and shorter suggestions
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $symptoms[] = $row['name'];
            }
        }
    }

    echo json_encode($symptoms);
    $conn->close();
?>

