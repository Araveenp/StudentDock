<?php
// Enable error reporting for debugging (disable this for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "student_dock"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$email = trim($_POST['email']);
$mobile = trim($_POST['mobile']);
$password = trim($_POST['password']);

// Sanitize email and mobile (additional validation could be added)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Prepare SQL query to check if user exists
$sql = "SELECT * FROM users WHERE email = ? AND mobile = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $mobile);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Start a session and store user info
        session_start();
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['user_email'] = $user['email']; // Store user email in session

        // Redirect to the home page
        header("Location: EDTHome.html");
        exit();
    } else {
        // Log the error and show a generic message to the user
        error_log("Invalid password attempt for user: " . $email);
        echo "Invalid credentials. Please try again.";
    }
} else {
    // Log the error and show a generic message to the user
    error_log("User not found: " . $email);
    echo "Invalid credentials. Please try again.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
