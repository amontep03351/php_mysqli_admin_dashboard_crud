<?php
// Database connection parameters
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
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $status = "1"; // Default status for a new user
    $level = "Admin"; // Default user level

    // Check if the username is already taken
    $checkUsernameQuery = "SELECT user_id FROM users WHERE user_username = ?";
    $checkUsernameStmt = $conn->prepare($checkUsernameQuery);
    $checkUsernameStmt->bind_param("s", $username);
    $checkUsernameStmt->execute();
    $checkUsernameStmt->store_result();

    if ($checkUsernameStmt->num_rows > 0) {
        echo "Username already taken. Please choose another.";
    } else {
        // Insert new user into the database
        $hashedPassword = hashPassword($password);
        $insertQuery = "INSERT INTO users (user_name, user_lname, user_username, user_password, user_status, user_level) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssssss", $fname, $lname, $username, $hashedPassword, $status, $level);

        if ($insertStmt->execute()) {
            echo "Registration successful. Welcome, " . $username;
            // You can set session variables or redirect the user to a login page here.
        } else {
            echo "Registration failed. Please try again.";
        }

        $insertStmt->close();
    }

    $checkUsernameStmt->close();
}

// Close the database connection
$conn->close();
?>
