<?php
// Database connection details
$host = 'localhost'; // Change if using a remote database
$dbname = 'student_dock';
$username = 'root';
$password = '';

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Max file size (100 MB)
$maxFileSize = 100 * 1024 * 1024;
$allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']; // Allowed file types

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if files were uploaded
    if (isset($_FILES['upfile']) && is_array($_FILES['upfile']['name'])) {
        $files = $_FILES['upfile'];

        // Loop through each uploaded file
        foreach ($files['name'] as $key => $fileName) {
            $fileTmpName = $files['tmp_name'][$key];
            $fileSize = $files['size'][$key];
            $fileType = $files['type'][$key];

            // Check for upload errors
            if ($files['error'][$key] !== 0) {
                echo "Error uploading file: " . htmlspecialchars($fileName) . ".<br>";
                continue;
            }

            // Check if the file is within the size limit
            if ($fileSize > $maxFileSize) {
                echo "File " . htmlspecialchars($fileName) . " exceeds the maximum allowed size of 100 MB.<br>";
                continue;
            }

            // Check if the file type is allowed
            if (!in_array($fileType, $allowedFileTypes)) {
                echo "File " . htmlspecialchars($fileName) . " has an invalid file type. Allowed types are: JPEG, PNG, GIF, and PDF.<br>";
                continue;
            }

            // Define the upload directory
            $uploadDir = 'uploads/';

            // Create the uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Sanitize the file name to avoid issues with special characters
            $sanitizedFileName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName);

            // Define the upload file path
            $filePath = $uploadDir . basename($sanitizedFileName);

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Prepare SQL query to insert file details into the database
                $stmt = $conn->prepare("INSERT INTO uploaded_files (file_name, file_path, file_size, file_type) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $sanitizedFileName, $filePath, $fileSize, $fileType);

                // Execute the query
                if ($stmt->execute()) {
                    echo "File " . htmlspecialchars($sanitizedFileName) . " uploaded and saved to the database successfully!<br>";
                } else {
                    echo "Error saving file information to the database for " . htmlspecialchars($sanitizedFileName) . ".<br>";
                }

                // Close statement
                $stmt->close();
            } else {
                echo "Failed to move the uploaded file " . htmlspecialchars($sanitizedFileName) . ".<br>";
            }
        }
    } else {
        echo "No files were uploaded or there was an error with the uploaded files.<br>";
    }
}

// Close the database connection
$conn->close();
?>
