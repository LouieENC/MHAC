<?php
     session_start();

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

    include '../includes/db_connect.php';

    // Query to select only students who are registered as users and have an active status
    $query = "
        SELECT * 
        FROM Students
    ";

    $result = $conn->query($query);

    $query1 = "SELECT s.student_id, s.reg_number, s.email, s.name, s.program_of_study,
                s.year_of_admission, s.expected_graduation_year, s.status, 
                s.date_of_birth, s.gender, s.phone_number,
                (CASE WHEN u.student_id IS NOT NULL THEN 'true' ELSE 'false' END) AS is_signed_in
          FROM Students s
          LEFT JOIN Users u ON s.student_id = u.student_id";
    $result1 = $conn->query($query1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - MUST Clinic Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/modals.css">
    <style>
        /* Responsive Table Wrapper */
        .table-container {
            width: 100%;
            max-height: 400px;
            overflow-x: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 20px;
            background-color: #f9f9f9;
        }

        .student-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            font-size: 0.9rem;
            text-align: left;
            table-layout: fixed;
        }

        .student-table th, .student-table td {
            padding: 12px;
            border: 1px solid #ddd;
            white-space: nowrap;
        }

        .student-table th {
            background-color: #007bff;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .student-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-check-record {
            padding: 5px 10px;
            font-size: 0.85rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-check-record:hover {
            background-color: #218838;
        }

        .filter-container {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .filter-container label {
            margin-right: 10px;
        }

        .filter-container {
            display: flex;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        #search-input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
            margin-right: 20px;
            transition: border-color 0.3s;
        }

        #search-input:focus {
            border-color: #007bff; /* Change border color on focus */
            outline: none; /* Remove outline */
        }

        .gender-filter {
            display: flex;
            align-items: center;
        }

        .gender-label {
            margin-right: 15px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .gender-label input[type="radio"] {
            display: none; /* Hide default radio buttons */
        }

        .gender-label span {
            position: relative;
            padding-left: 25px; /* Space for custom radio */
        }

        .gender-label span:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #007bff; /* Border color */
            border-radius: 50%; /* Circle */
            background: white; /* Background color */
        }

        .gender-label input[type="radio"]:checked + span:before {
            background: #007bff; /* Background color when checked */
        }

        .gender-label input[type="radio"]:checked + span:after {
            content: '';
            position: absolute;
            left: 4px; /* Position for inner dot */
            top: 50%;
            transform: translateY(-50%);
            width: 8px; /* Inner dot size */
            height: 8px; /* Inner dot size */
            border-radius: 50%; /* Circle */
            background: white; /* Inner dot color */
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

        <!-- Medical Record Modal -->
        <div id="medicalRecordModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Student Medical Record</h2>
                <div id="medicalRecordDetails">
                    <!-- Medical record details will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
          <header class="header">
              <h1>Manage Patients</h1>
          </header>

            <div class="filter-container">
                <input type="text" id="search-input" placeholder="Search by name" onkeyup="filterStudents()">
                
                <!-- Gender Filter -->
                <label><input type="radio" name="gender" value="All" onclick="filterStudents()" checked> All</label>
                <label><input type="radio" name="gender" value="Male" onclick="filterStudents()"> Male</label>
                <label><input type="radio" name="gender" value="Female" onclick="filterStudents()"> Female</label>
                |
                <!-- Status Filter -->
                <label><input type="radio" name="status" value="All" onclick="filterStudents()" checked> All</label>
                <label><input type="radio" name="status" value="Active" onclick="filterStudents()"> Active</label>
                <label><input type="radio" name="status" value="Graduated" onclick="filterStudents()"> Graduated</label>
                <label><input type="radio" name="status" value="Withdrawn" onclick="filterStudents()"> Withdrawn</label>

                <!-- Signed-in Users Filter -->
                <label><input type="checkbox" id="signed-in-filter" onclick="filterStudents()"> Signed-in Users</label>
            </div>

            <div id="not-found-message" style="display: none; color: red; margin-top: 20px;">
                No matching records found.
            </div>




            <div class="table-container">
                <table class="student-table" id="student-table">
                    <thead>
                        <tr>
                            <th>Registration Number</th>
                            <th>Name</th>
                            <th>Program of Study</th>
                            <th>Status</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result1->fetch_assoc()): ?>
                            <tr data-signed-in="<?php echo $row['is_signed_in']; ?>">
                                <td><?php echo $row['reg_number']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['program_of_study']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td>
                                    <form action="fetch_medical_record.php" method="get">
                                        <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                        <button type="button" class="btn-check-record" onclick="openModal(<?php echo $row['student_id']; ?>)">
                                            Check Medical Record
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    
  

    <script src="../assets/js/script.js"></script>
    <script>
        function openModal(studentId) {
            // Fetch the medical record data via AJAX
            fetch(`../includes/fetch_medical_record.php?student_id=${studentId}`)
                .then(response => response.text())
                .then(data => {
                    // Populate the modal with fetched data
                    document.getElementById('medicalRecordDetails').innerHTML = data;
                    // Display the modal
                    document.getElementById('medicalRecordModal').style.display = 'block';
                    document.getElementById('medicalRecordModal').style.animation = 'fadeIn 0.5s';
                })
                .catch(error => console.error('Error fetching medical record:', error));
        }

        function closeModal() {
            document.getElementById('medicalRecordModal').style.display = 'none';
        }

        function filterStudents() {
            let searchInput = document.getElementById('search-input').value.toLowerCase();
            let gender = document.querySelector('input[name="gender"]:checked').value;
            let status = document.querySelector('input[name="status"]:checked').value;
            let signedIn = document.getElementById('signed-in-filter').checked;

            let table = document.getElementById('student-table');
            let rows = table.getElementsByTagName('tr');
            let found = false;

            for (let i = 1; i < rows.length; i++) {
                let nameCell = rows[i].getElementsByTagName('td')[1];
                let genderCell = rows[i].getElementsByTagName('td')[4];
                let statusCell = rows[i].getElementsByTagName('td')[3];
                let isSignedIn = rows[i].dataset.signedIn === "true";  // Assumes data-signed-in attribute

                if (nameCell && genderCell && statusCell) {
                    let name = nameCell.textContent.toLowerCase();
                    let rowGender = genderCell.textContent.trim();
                    let rowStatus = statusCell.textContent.trim();

                    // Check if the row matches all filters
                    let nameMatches = name.includes(searchInput);
                    let genderMatches = (gender === 'All' || rowGender === gender);
                    let statusMatches = (status === 'All' || rowStatus === status);
                    let signedInMatches = (!signedIn || isSignedIn);

                    if (nameMatches && genderMatches && statusMatches && signedInMatches) {
                        rows[i].style.display = '';
                        found = true;
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }

            document.getElementById('not-found-message').style.display = found ? 'none' : 'block';
        }

        // Update the input events to call the combined function
        document.getElementById('search-input').addEventListener('keyup', filterStudents);
        document.querySelectorAll('input[name="gender"]').forEach(radio => radio.addEventListener('change', filterStudents));
        document.querySelectorAll('input[name="status"]').forEach(radio => radio.addEventListener('change', filterStudents));
        document.getElementById('signed-in-filter').addEventListener('change', filterStudents);

    </script>
</body>
</html>
