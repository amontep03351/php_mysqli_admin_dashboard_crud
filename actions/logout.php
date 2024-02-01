<?php session_start(); ?>
<?php session_destroy(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 100px;
        }
        h1 {
            color: #FF0000; /* Red color for emphasis */
        }
    </style>
    <script>
        // Clear session and redirect to index.php after 5 seconds
        setTimeout(function() {
            // Clear session (replace this with your actual session clearing code)
            // Example: sessionStorage.clear(); // For client-side session
            //          session_unset(); // For server-side session

            // Redirect to index.php
            window.location.href = '../index.html';
        }, 5000);
    </script>
</head>
<body>
    <h1>Logout Successful</h1>
    <p>You have been logged out. You will be redirected to the home page in 5 seconds.</p>
    <p>If you are not redirected, <a href="../index.html">click here</a>.</p>
</body>
</html>
