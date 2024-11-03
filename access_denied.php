<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link rel="stylesheet" href="./assets/style.css"> 
    <style>
        /* Custom styles for Access Denied page */
        .access-denied-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f2f5;
            text-align: center;
        }

        .access-denied-container h1 {
            font-size: 48px;
            color: #4f46e5;
            margin-bottom: 20px;
        }

        .access-denied-container p {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
        }

        .access-denied-container a {
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .access-denied-container a:hover {
            background-color: #3730a3;
        }
    </style>
</head>
<body>

    <div class="access-denied-container">
        <h1>Access Denied</h1>
        <p>You do not have permission to view this page.</p>
        <a href="login-signup.php">Return to Homepage</a>
    </div>

</body>
</html>
