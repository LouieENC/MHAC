<?php
    session_start();
    require '../includes/db_connect.php'; // Include your database connection

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

    // Check if update_id is present in the URL
    $doctorToUpdate = null;
    if (isset($_GET['update_id'])) {
        $update_id = $_GET['update_id'];
        $result = $conn->query("SELECT * FROM doctors WHERE doctor_id = '$update_id'");
        if ($result && $result->num_rows > 0) {
            $doctorToUpdate = $result->fetch_assoc(); // Fetch doctor data
        }
    }

    // CRUD Operations

    // Add Doctor
    if (isset($_POST['add_doctor'])) {
        $name = $_POST['name'];
        $specialization = $_POST['specialization'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $bio = $_POST['bio'];
        $experience = $_POST['years_of_experience'];

        $query = "INSERT INTO doctors (name, specialization, email, phone_number, bio, years_of_experience) 
                  VALUES ('$name', '$specialization', '$email', '$phone', '$bio', '$experience')";
        if ($conn->query($query) === TRUE) {
            $message = "Doctor added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }

    // Update Doctor
    if (isset($_POST['update_doctor'])) {
        $doctor_id = $_POST['doctor_id'];
        $name = $_POST['name'];
        $specialization = $_POST['specialization'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $experience = $_POST['years_of_experience'];

        $query = "UPDATE doctors SET name='$name', specialization='$specialization', email='$email', phone_number='$phone',
                  years_of_experience='$experience' WHERE doctor_id='$doctor_id'";
        if ($conn->query($query) === TRUE) {
            $message = "Doctor updated successfully!";
        } else {
            $message = "Error updating doctor: " . $conn->error;
        }
    }

    // Delete Doctor
    if (isset($_GET['delete_id'])) {
        $doctor_id = $_GET['delete_id'];
        $query = "DELETE FROM doctors WHERE doctor_id='$doctor_id'";
        if ($conn->query($query) === TRUE) {
            $message = "Doctor deleted successfully!";
        } else {
            $message = "Error deleting doctor: " . $conn->error;
        }
    }

    // Fetch Doctors
    $result = $conn->query("SELECT * FROM doctors");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/doctors.css">
    <script src="../assets/js/doctors.js" defer></script>
</head>
<body>
    <!-- Burger Menu Icon for Mobile -->
    <button class="burger"><i class="fas fa-bars"></i></button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay"></div>

    <div class="dashboard-container">
        <?php include '../includes/nav.php'; ?>
        <div class="main-content doctor-page">
            <h1>Manage Doctors</h1>

            <!-- Add Doctor Form -->
            <div class="form-container">
                <form class="doctor-form" action="doctors.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="specialization">Specialization:</label>
                        <select id="specialization" name="specialization" required>
                            <option value="General Practitioner">General Practitioner</option>
                            <option value="Pediatrician">Pediatrician</option>
                            <option value="Cardiologist">Cardiologist</option>
                            <option value="Neurologist">Neurologist</option>
                            <option value="Orthopedist">Orthopedist</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" id="phone_number" name="phone_number">
                    </div>

                    <div class="form-group">
                        <label for="years_of_experience">Years of Experience:</label>
                        <input type="number" id="years_of_experience" name="years_of_experience" min="0">
                    </div>

                    <div class="form-group">
                        <input type="submit" name="add_doctor" value="Add Doctor">
                    </div>
                </form>
            </div>

            <!-- Display Doctors -->
            <table class="doctor-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Years of Experience</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['specialization']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone_number']; ?></td>
                                <td><?php echo $row['years_of_experience']; ?></td>
                                <td>
                                <button class="update-button" onclick="window.location.href='doctors.php?update_id=<?php echo $row['doctor_id']; ?>'">
                                    Update
                                </button>
                                    <a class="delete-doctor-link" href="doctors.php?delete_id=<?php echo $row['doctor_id']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">No doctors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Update Doctor Modal -->
            <div id="updateModal" class="modal" style="<?php echo isset($doctorToUpdate) ? 'display: block;' : 'display: none;'; ?>">
                <div class="modal-content">
                    <span class="close-button" onclick="window.location.href='doctors.php'">&times;</span>
                    <h2>Update Doctor</h2>
                    <form id="updateDoctorForm" action="doctors.php" method="POST">
                        <input type="hidden" id="doctor_id" name="doctor_id" value="<?php echo $doctorToUpdate['doctor_id'] ?? ''; ?>" required>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="update_name" name="name" value="<?php echo $doctorToUpdate['name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="specialization">Specialization:</label>
                            <select id="update_specialization" name="specialization" required>
                                <option value="General Practitioner" <?php echo (isset($doctorToUpdate) && $doctorToUpdate['specialization'] == 'General Practitioner') ? 'selected' : ''; ?>>General Practitioner</option>
                                <option value="Pediatrician" <?php echo (isset($doctorToUpdate) && $doctorToUpdate['specialization'] == 'Pediatrician') ? 'selected' : ''; ?>>Pediatrician</option>
                                <option value="Cardiologist" <?php echo (isset($doctorToUpdate) && $doctorToUpdate['specialization'] == 'Cardiologist') ? 'selected' : ''; ?>>Cardiologist</option>
                                <option value="Neurologist" <?php echo (isset($doctorToUpdate) && $doctorToUpdate['specialization'] == 'Neurologist') ? 'selected' : ''; ?>>Neurologist</option>
                                <option value="Orthopedist" <?php echo (isset($doctorToUpdate) && $doctorToUpdate['specialization'] == 'Orthopedist') ? 'selected' : ''; ?>>Orthopedist</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="update_email" name="email" value="<?php echo $doctorToUpdate['email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="update_phone_number" name="phone_number" value="<?php echo $doctorToUpdate['phone_number'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio:</label>
                            <textarea id="update_bio" name="bio" rows="3"><?php echo $doctorToUpdate['bio'] ?? ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="years_of_experience">Years of Experience:</label>
                            <input type="number" id="update_years_of_experience" name="years_of_experience" min="0" value="<?php echo $doctorToUpdate['years_of_experience'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="update_doctor" value="Update Doctor">
                        </div>
                    </form>
                </div>
            </div>



            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function openModal(doctor) {
            document.getElementById("doctor_id").value = doctor.doctor_id;
            document.getElementById("update_name").value = doctor.name;
            document.getElementById("update_specialization").value = doctor.specialization;
            document.getElementById("update_email").value = doctor.email;
            document.getElementById("update_phone_number").value = doctor.phone_number;
            document.getElementById("update_bio").value = doctor.bio;
            document.getElementById("update_years_of_experience").value = doctor.years_of_experience;
            document.getElementById("updateModal").style.display = "block";
        }


        function closeModal() {
            document.getElementById("updateModal").style.display = "none";
        }
    </script>
</body>
</html>
