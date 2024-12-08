<?php
// Enable error reporting (for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "student_dock"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate phone number
function isValidPhoneNumber($number) {
    return preg_match('/^[0-9]{10,15}$/', $number);
}

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get form data
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$email = trim($_POST['email']);
$num = trim($_POST['num']);
$password = trim($_POST['pass']);

// Validate input
if (!isValidPhoneNumber($num)) {
    die("Invalid phone number. Please enter a valid mobile number (10-15 digits).");
}

if (!isValidEmail($email)) {
    die("Invalid email format.");
}

// Check if email already exists
$emailCheckQuery = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($emailCheckQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Email already registered. Please use a different email.");
}

// Validate password strength
if (strlen($password) < 8) {
    die("Password must be at least 8 characters long.");
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into the database
$sql = "INSERT INTO users (first_name, last_name, email, mobile, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $fname, $lname, $email, $num, $hashedPassword);

if ($stmt->execute()) {
    // Redirect to EDTHome.html after successful registration
    header("Location: EDTHome.html");
    exit();
} else {
    // Log the error and show a generic message to the user
    error_log("Error: " . $stmt->error);
    echo "Something went wrong. Please try again later.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
