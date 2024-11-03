<?php
    // profile.php
    include "../includes/db_connect.php";

    session_start();

    $timeout_duration = 1800;

    // Check if the user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ../login-signup.php");
        exit();
    }

    // Check for session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../login-signup.php?message=Session expired. Please log in again.");
        exit();
    }

    $_SESSION['last_activity'] = time();  // Update last activity time

    // Check user role
    if (!isset($_SESSION['role'])) {
        header("Location: ../access_denied.php");
        exit();
    }

    // Fetch user profile data based on role
    if ($_SESSION['role'] == 'Student') {
        $user_id = $_SESSION['student_id'];  // Use student_id for students
        $query = "SELECT * FROM Students WHERE student_id = ?";
    } elseif ($_SESSION['role'] == 'Doctor') {
        $user_id = $_SESSION['doctor_id'];  // Use doctor_id for doctors
        $query = "SELECT * FROM Doctors WHERE doctor_id = ?";
    } else {
        header("Location: ../access_denied.php");
        exit();
    }

    // Execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        echo "<p>Error fetching profile data.</p>";
    }

    // Fetch existing medical record data for the logged-in user
    $medical_record = [
        'weight' => '',
        'height' => '',
        'blood_group' => '',
        'chronic_conditions' => '',
        'allergies' => '',
    ];

    $query = "SELECT * FROM MedicalRecords WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $medical_record = $result->fetch_assoc();
    }

    $student_id = $_SESSION['student_id'];

    // Retrieve student name
    $studentQuery = $conn->query("SELECT name FROM Students WHERE student_id = $student_id");
    $studentName = $studentQuery->fetch_assoc()['name'] ?? 'Student';
?>


<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/user-page-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/modals.css">
    <title>Student Profile - MUST Clinic Online</title>
    <style>
        /* Profile Section */
        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex; /* Add flex display */
            flex-direction: column; /* Stack children vertically */
            align-items: unset; /* Center items horizontally */
        }
        .photo{
            margin: auto; /* Add flex display */
            flex-direction: column; /* Stack children vertically */
            align-items: unset; /* Center items horizontally */
        }

        .profile-photo {
            width: 200px;
            height: 200px;
            background-color: #e9ecef;
            border-radius: 90px;
            position: relative;
            overflow: hidden; /* Prevents overflow */
            display: flex; /* Flexbox for centering the image */
            justify-content: center; /* Center image horizontally */
            align-items: center; /* Center image vertically */
        }

        .profile-details {
            padding: 80px 20px 20px; /* Adjusted for profile photo overlap */
        }

        .profile-details h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .profile-details div {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .profile-details div:last-child {
            border-bottom: none; /* Remove border from last item */
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($studentName); ?></h1>
        <p>Your personalized space for managing your health</p>
    </header>

    <!-- Sidebar containing nav -->
    <nav class="top-nav">
        <ul>
            <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#profile" class="active"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="student_dashboard.php"><i class="fas fa-video"></i> Consultation</a></li>
            <li><a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>   

    <!-- Profile Container -->
    <div class="profile-container">
        <div class="photo">
            <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" alt="Profile Photo" class="profile-photo">
        </div>

        <div class="profile-details">
            <h2><?php echo htmlspecialchars($user_data['name']); ?></h2>
            <div>
                <span>Email: </span>
                <span><?php echo htmlspecialchars($user_data['email']); ?></span>
            </div>
            <div>
                <span>Registration Number: </span>
                <span><?php echo htmlspecialchars($user_data['reg_number']); ?></span>
            </div>
            <div>
                <span>Program of Study: </span>
                <span><?php echo htmlspecialchars($user_data['program_of_study']); ?></span>
            </div>
            <div>
                <span>Year of Admission: </span>
                <span><?php echo htmlspecialchars($user_data['year_of_admission']); ?></span>
            </div>
            <div>
                <span>Expected Graduation Year: </span>
                <span><?php echo htmlspecialchars($user_data['expected_graduation_year']); ?></span>
            </div>
            <div>
                <span>Status: </span>
                <span><?php echo htmlspecialchars($user_data['status']); ?></span>
            </div>
            <div>
                <span>Date of Birth: </span>
                <span><?php echo htmlspecialchars($user_data['date_of_birth']); ?></span>
            </div>
            <div>
                <span>Gender: </span>
                <span><?php echo htmlspecialchars($user_data['gender']); ?></span>
            </div>
            <div>
                <span>Phone Number: </span>
                <span><?php echo htmlspecialchars($user_data['phone_number']); ?></span>
            </div>
        </div>

        <center><button class="card-btn" onclick="openModal()">Medical Record</button></center>

        <!-- Medical Record Modal -->
        <div id="medicalRecordModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Medical Record</h2>
                <!-- Form sends data directly to PHP for processing -->
                <form id="medicalRecordForm" action="./update_medical_record.php" method="POST">
                    <label>Weight (kg):</label>
                    <input type="number" step="0.01" id="weight" name="weight" value="<?php echo htmlspecialchars($medical_record['weight']); ?>" required>

                    <label>Height (cm):</label>
                    <input type="number" step="0.01" id="height" name="height" value="<?php echo htmlspecialchars($medical_record['height']); ?>" required>

                    <label>Blood Group:</label>
                    <input type="text" id="bloodGroup" name="blood_group" value="<?php echo htmlspecialchars($medical_record['blood_group']); ?>" required>

                    <label>Chronic Conditions:</label>
                    <textarea id="chronicConditions" name="chronic_conditions"><?php echo htmlspecialchars($medical_record['chronic_conditions']); ?></textarea>

                    <label>Allergies:</label>
                    <textarea id="allergies" name="allergies"><?php echo htmlspecialchars($medical_record['allergies']); ?></textarea>

                    <!-- Submitting will post directly to update_medical_record.php -->
                    <button class="card-btn" type="submit">Save Changes</button>
                </form>
            </div>
        </div>




        <div class="social-media-card">
            <h3>Connect with Me</h3>
            <br>
            <div class="social-media">
                <span class="fa-stack fa-sm">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fab fa-facebook fa-stack-1x fa-inverse"></i>
                </span>
                <span class="fa-stack fa-sm">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                </span>
                <span class="fa-stack fa-sm">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fab fa-instagram fa-stack-1x fa-inverse"></i>
                </span>
                <span class="fa-stack fa-sm">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fab fa-github fa-stack-1x fa-inverse"></i>
                </span>
                <span class="fa-stack fa-sm">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fab fa-linkedin fa-stack-1x fa-inverse"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy-policy">Privacy Policy</a>
                <a href="#terms-of-service">Terms of Service</a>
                <a href="#contact-us">Contact Us</a>
            </div>
            <br>
            <p>&copy; 2024 MUST Online Clinic. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="../assets/js/user-page-scripts.js"></script>
    <script>
        // Function to open modal and fetch current medical record data
        function openModal() {
            document.getElementById('medicalRecordModal').style.display = 'block';
            fetchMedicalRecord(); // Fetch current data to populate modal fields
        }

        // Function to close modal
        function closeModal() {
            document.getElementById('medicalRecordModal').style.display = 'none';
        }

        // Fetch current data from the database and populate fields
        function fetchMedicalRecord() {
            fetch('fetch_medical_record.php') // Replace with actual PHP path
                .then(response => response.json())
                .then(data => {
                    if (!data.error) {
                        // Populate fields with existing data
                        document.getElementById('weight').value = data.weight || '';
                        document.getElementById('height').value = data.height || '';
                        document.getElementById('bloodGroup').value = data.blood_group || '';
                        document.getElementById('chronicConditions').value = data.chronic_conditions || '';
                        document.getElementById('allergies').value = data.allergies || '';
                    } else {
                        alert(data.error); // Show error if no record found
                    }
                })
                .catch(error => console.error('Error fetching medical record:', error));
        }

        // Function to save updates to medical record
        function updateMedicalRecord() {
            // Gather form data to send to server
            const formData = new FormData(document.getElementById('medicalRecordForm'));

            fetch('update_medical_record.php', { // Replace with actual PHP path
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); // Notify user of success
                    closeModal(); // Optionally close modal on successful save
                } else {
                    alert(data.message); // Show error message on failure
                }
            })
            .catch(error => console.error('Error updating medical record:', error));
        }

        // Event listener for modal close button
        document.querySelector('.close').addEventListener('click', closeModal);



    </script>
</body>
</html>