<?php
session_start();
include 'connect.php';
// Function to securely hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Function to verify hashed password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // SQL query to fetch user details
    $sql = "SELECT user_id, user_username, user_password FROM users WHERE user_username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user with the provided username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $dbUsername, $dbPassword);
        $stmt->fetch();

        // Verify the entered password against the hashed password in the database
        if (verifyPassword($password, $dbPassword)) {
            $_SESSION['userId'] = $userId;
            $_SESSION['Username'] = $dbUsername;
            header( "Location: loginsuccessful.html" );
            // You can set session variables or redirect the user to a dashboard here.
        } else {
            header( "Location: incorrectpassword.html" );
        }
    } else {

        header( "Location: Usernotfound.html" );
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
 ?>
