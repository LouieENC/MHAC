<?php
// includes/db_connection.php

$host = 'localhost:3308';
$db   = 'mhac_db';
$user = 'root';
$password = '';
$sqlFile = __DIR__ . '../db/mhac_db.sql'; // Specify the path to our db.sql file

// Establishes the connection without specifying the database first
$conn = new mysqli($host, $user, $password);

// Check if the connection was successful
if ($conn->connect_error) {
    error_log('Database Connection Error: ' . $conn->connect_error);
    echo '<script>alert("Database connection failed");</script>';
    exit('Database connection failed.');
}

// Check if the database exists
if (!$conn->select_db($db)) {
    // Database doesn't exist, attempt to create it
    $createDbQuery = "CREATE DATABASE IF NOT EXISTS $db";
    if ($conn->query($createDbQuery) === TRUE) {
        echo '<script>console.log("Database created successfully");</script>';
        
        // it Selects the newly created database
        $conn->select_db($db);
        
        // Imports the SQL file
        if (file_exists($sqlFile)) {
            $sqlContent = file_get_contents($sqlFile);
            $queries = explode(";", $sqlContent);
            foreach ($queries as $query) {
                if (trim($query)) {
                    $conn->query($query);
                }
            }
            echo '<script>console.log("Database imported successfully from db.sql");</script>';
        } else {
            error_log('SQL file not found at: ' . $sqlFile);
            echo '<script>alert("SQL file not found.");</script>';
        }
    } else {
        error_log('Database Creation Error: ' . $conn->error);
        echo '<script>alert("Database creation failed");</script>';
        exit('Database creation failed.');
    }
} else {
    echo '<script>console.log("Database connection successful");</script>';
}
?>
