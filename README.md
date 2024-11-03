# MHAC
Must Health Awareness and Consultancy System, #html, #css, #php, #javascript. 
make sure you have changed the port according to you port for xampp or wampp in the db_connect.php


like this arrangement:

$host = 'localhost';   // Hostname
$port = 3308;          // Port number
$db   = 'mhac_db';     // Database name
$user = 'root';        // MySQL username
$password = '';        // MySQL password (leave empty if no password)

// Establish the connection using MySQLi and assign it to $conn
$conn = new mysqli($host, $user, $password, $db, $port);  // Correctly passing parameters
