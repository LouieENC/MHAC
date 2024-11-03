<?php
session_start();

include '../includes/db_connect.php';

$timeout_duration = 1800;

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login-signup.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../login-signup.php?message=Session expired. Please log in again.");
    exit();
}

$_SESSION['last_activity'] = time();

// Check user role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../access_denied.php");
    exit();
} 

// Initialize a variable to store feedback messages
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['condition_id'])) {
    $condition_id = $_POST['condition_id']; // Get the condition ID
    $health_tip = $_POST['health_tip']; // Get the health tip for the specific condition

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE Conditions SET health_tips = ? WHERE condition_id = ?");
    
    // Bind parameters
    $stmt->bind_param("si", $health_tip, $condition_id);
    
    // Execute the statement and handle success or error
    if ($stmt->execute()) {
        $message = "Successfully updated health tips for condition ID $condition_id.";
    } else {
        $message = "Error updating health tips for condition ID $condition_id: " . $stmt->error;
    }
    
    // Close the prepared statement
    $stmt->close();
}

// Fetch conditions for the form
$conditions_result = $conn->query("SELECT condition_id, name, health_tips FROM Conditions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tips - MUST Clinic Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .health_tips_form {
            max-height: 400px; /* Adjust as needed */
            overflow-y: auto; /* Enables vertical scrolling */
            border-radius: 10px;

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            position: sticky; 
            top: 0;
            background-color: #4f46e5; 
            color: white;
            z-index: 10;
             
        }

        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9; 
        }

        textarea {
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical; 
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); 
            font-size: 14px; 
        }

        textarea:focus {
            outline: none; 
            border-color: #4CAF50; 
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5); 
        }

        button {
            background-color: #4f46e5;
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s ease; 
        }

        button:disabled {
            background-color: #ccc; 
            cursor: not-allowed; 
        }
        /* Responsive styles */
        @media (max-width: 600px) {
            table {
                font-size: 14px;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Burger Menu Icon for Mobile -->
    <button class="burger"><i class="fas fa-bars"></i></button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <div class="dashboard-container">
        <!-- Side Navigation -->
        <?php include '../includes/nav.php'; ?>
        <!-- Main Content -->
        <div class="main-content">
          <header class="header">
              <h1>Health Tips</h1>
          </header>

            <div class="health_tips_form">
                <!-- Display feedback message -->
                <?php if (!empty($message)): ?>
                    <div>
                        <strong>Feedback:</strong><br>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST"> 
                    <table>
                        <thead>
                            <tr>
                                <th>Condition Name</th>
                                <th>Current Health Tips</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($conditions_result->num_rows > 0) {
                                while ($row = $conditions_result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td><textarea name='health_tip' rows='3'>" . htmlspecialchars($row['health_tips']) . "</textarea></td>";
                                    echo "<td>
                                            <form action='' method='POST'>
                                                <input type='hidden' name='condition_id' value='" . $row['condition_id'] . "'>
                                                <button type='submit'>Update Health Tip</button>
                                            </form>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No conditions found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
