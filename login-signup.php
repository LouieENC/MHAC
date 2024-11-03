<?php
    //login-signup.php
    include './includes/db_connect.php';

    session_start();  // Start the session

    // Check if the user is already logged in and has a role
    if (isset($_SESSION['role'])) {
        // Redirect based on user role
        $role = $_SESSION['role'];

        if ($role === 'Admin') {
            header('Location: ./admin/admin_dashboard.php');
            exit(); // Stop further execution of the script
        } elseif ($role === 'Doctor') {
            header('Location: ./doctor/doctor_dashboard.php');
            exit();
        } elseif ($role === 'Student') {
            header('Location: ./patient/student_dashboard.php');
            exit();
        }
    }

    // Handle login or registration messages if the user is not logged in
    $message = '';
    if (isset($_SESSION['register_error'])) {
        $message = $_SESSION['register_error'];
        unset($_SESSION['register_error']);  // Clear the error after showing it
    } elseif (isset($_SESSION['register_success'])) {
        $message = $_SESSION['register_success'];
        unset($_SESSION['register_success']);  // Clear the success after showing it
    } elseif (isset($_SESSION['error'])) {
        $message = $_SESSION['error'];
        unset($_SESSION['error']);  // Clear the login error message
    } elseif (isset($_SESSION['success'])) {
        $message = $_SESSION['success'];
        unset($_SESSION['success']);  // Clear the login success message
    } elseif (isset($_GET['message'])) {
        $message = $_GET['message'];  // For session timeout or other GET messages
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUST Clinic Online - Login/Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/login-signup.css">
    <style>
        .slideshow-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        .slide {
            display: none;
            text-align: center;
        }

        .dots {
            text-align: center;
            padding: 10px;
        }

        .dot {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
            cursor: pointer;
        }

        .active, .dot:hover {
            background-color: #717171;
        }

        .note{
            color:red;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <img src="./assets/img/must-logo.png" alt="EasyCare Logo">
                <h1>MUST Clinic Online</h1>
                <p>Connecting You to Better Health</p>
            </div>
            <div class="login-options">
                <h2 id="form-title">Login to your account</h2>

                <!-- Login Form -->
                <form id="loginForm" action="./includes/login.php" method="POST" class="fade-in" onsubmit="return validateLoginForm()">
                    <!-- Message Display -->
                    <div id="messageBox" style="color: white; margin-top: 5px; margin-bottom: 10px;">
                        <?php if ($message) echo $message; ?>
                    </div>
                    <div class="form-group">
                        <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                        <i class="fa fa-eye" onclick="togglePasswordVisibility('loginPassword')"></i>
                    </div>
                    <button type="submit" class="btn">Login</button>
                    <p>Don't have an account? <span class="toggle-link" onclick="toggleForm('register')">Register here</span></p>
                </form>

                <!-- Registration Form -->
                <form id="registerForm" action="./includes/register.php" method="POST" style="display: none;" class="fade-in" onsubmit="return validateRegisterForm()">
                    <!-- Message Display -->
                    <div id="messageBox" style="color: white; margin-bottom: 10px;">
                        <?php if ($message) echo $message; ?>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="registerPassword" name="password" placeholder="Password" required>
                        <i class="fa fa-eye" onclick="togglePasswordVisibility('registerPassword')"></i>
                    </div>
                    <button type="submit" class="btn">Register</button>
                    <p>Already have an account? <span class="toggle-link" onclick="toggleForm('login')">Login here</span></p>
                </form>
            </div>
        </div>

        <div class="welcome-box">
            <div class="slideshow-container">
                <!-- Slide 1: System Instructions -->
                <div class="slide fade"> 
                    <h2>System Instructions</h2> 
                    <p>Welcome to the MUST Clinic Online System! We are dedicated to making healthcare accessible to you, facilitating remote consultations, self-diagnosis, and medical management in one user-friendly platform.</p>
                    <ul> 
                        <li><strong>Login:</strong> To log in, use the email you were assigned by MUST.</li> 
                        <li><strong>Register:</strong> If you are not registered on this system, click register link, then please input your MUST email and a password of your choice.</li> 
                    </ul>
                </div>

                <!-- Slide 2: Easy Appointments -->
                <div class="slide fade note">
                    <h2>Note!</h2>
                    <p>The system assumes you are already registered as a student. Therefore if you are not, go and register first before you try this system</p>
                </div>

                <!-- Slide 3: Join the Community -->
                <div class="slide fade">
                    <h2>Join the Community</h2>
                    <p>Become a part of a growing community committed to better health. Start your journey with us today!</p>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="tel:+123456789"><i class="fas fa-phone-alt"></i></a>
                    </div>
                </div>
            </div>

            <!-- Navigation Dots -->
            <div class="dots">
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
            </div>
        </div>
    </div>

    <script>
        
        let slideIndex = 0;

        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            let dots = document.getElementsByClassName("dot");

            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1; }
            
            slides[slideIndex - 1].style.display = "block";

            for (let i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            
            dots[slideIndex - 1].className += " active";
            setTimeout(showSlides, 7000); // Changes slide every 7 seconds
        }

        function currentSlide(n) {
            slideIndex = n - 1; // Sets to zero-based index
            showSlides();
        }

        // Initialize the slideshow
        document.addEventListener("DOMContentLoaded", showSlides);


        // JavaScript to toggle between login and register forms
        function toggleForm(activeForm) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const formTitle = document.getElementById('form-title');

            if (activeForm === 'login') {
                loginForm.style.display = "block";
                registerForm.style.display = "none";
                formTitle.textContent = "Login to your account";
            } else {
                loginForm.style.display = "none";
                registerForm.style.display = "block";
                formTitle.textContent = "Register as a student";
            }
        }

        // Function to toggle password visibility
        function togglePasswordVisibility(passwordFieldId) {
            const passwordField = document.getElementById(passwordFieldId);
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        }

        // Email validation: must end with "@must.ac.mw"
        function validateEmail(email) {
            const emailPattern = /^[a-zA-Z0-9._%+-]+@must\.ac\.mw$/;
            return emailPattern.test(email);
        }

        // Password validation: at least 8 characters, with at least one uppercase, one lowercase, one digit, and one special character
        function validatePassword(password) {
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            return passwordPattern.test(password);
        }

        // Form validation for login
        function validateLoginForm() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const messageBox = document.getElementById('messageBox');
            let isValid = true;
            messageBox.innerHTML = ''; // Clear previous messages

            if (!email) {
                setError('loginEmail', 'Email is required.');
                isValid = false;
            } else if (!validateEmail(email)) {
                setError('loginEmail', 'Invalid email. Please use a MUST email (e.g., user@must.ac.mw).');
                isValid = false;
            } else {
                setSuccess('loginEmail');
            }

            if (!password) {
                setError('loginPassword', 'Password is required.');
                isValid = false;
            } else {
                setSuccess('loginPassword');
            }

            return isValid;
        }

        // Form validation for registration
        function validateRegisterForm() {
            const regNumber = document.forms["registerForm"]["reg_number"].value;
            const name = document.forms["registerForm"]["name"].value;
            const email = document.forms["registerForm"]["email"].value;
            const password = document.forms["registerForm"]["password"].value;
            const messageBox = document.getElementById('messageBox');
            let isValid = true;
            messageBox.innerHTML = ''; // Clear previous messages

            if (!regNumber) {
                setError('registerForm', 'reg_number', 'Registration number is required.');
                isValid = false;
            } else {
                setSuccess('registerForm', 'reg_number');
            }

            if (!name) {
                setError('registerForm', 'name', 'Full name is required.');
                isValid = false;
            } else {
                setSuccess('registerForm', 'name');
            }

            if (!email) {
                setError('registerForm', 'email', 'Email is required.');
                isValid = false;
            } else if (!validateEmail(email)) {
                setError('registerForm', 'email', 'Invalid email. Please use a MUST email (e.g., user@must.ac.mw).');
                isValid = false;
            } else {
                setSuccess('registerForm', 'email');
            }

            if (!password) {
                setError('registerForm', 'password', 'Password is required.');
                isValid = false;
            } else if (!validatePassword(password)) {
                setError('registerForm', 'password', 'Password must be at least 8 characters, include uppercase, lowercase, a number, and a special character.');
                isValid = false;
            } else {
                setSuccess('registerForm', 'password');
            }

            return isValid;
        }

        // Function to show error message and highlight field
        function setError(formId, fieldId, message) {
            const field = document.forms[formId][fieldId];
            const errorMessage = document.createElement('div');
            errorMessage.innerText = message;
            errorMessage.style.color = 'red';
            errorMessage.style.fontSize = '12px';
            field.style.border = '1px solid red';
            field.parentNode.appendChild(errorMessage);
        }

        // Function to show success and highlight field
        function setSuccess(formId, fieldId) {
            const field = document.forms[formId][fieldId];
            field.style.border = '1px solid green';
        }

        // Show a message in the message box on page load
        window.onload = function() {
            const message = "<?php echo $message; ?>";
            if (message) {
                document.getElementById('messageBox').innerText = message;  // Show the message in the message box
            }
        };

    </script>
</html>
