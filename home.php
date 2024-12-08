<?php
// Function to manually load environment variables from a .env file
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        die("Error: .env file not found at " . $filePath);
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse key=value pairs
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Set in $_ENV
        $_ENV[$key] = $value;
    }
}

// Call the function to load the .env file
$envFilePath = __DIR__ . '/.env';
loadEnv($envFilePath);

// Access environment variables
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? 'student_dock';
$dbUser = $_ENV['DB_USER'] ?? 'root';
$dbPass = $_ENV['DB_PASS'] ?? '';

// Establish a database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to $dbName.";
}

// Rest of your HTML and file handling code follows below...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>STUDENTDOCK</title>
    <style>
        body {
            background-color: rgb(187, 240, 240);
            height: 99vh;
            justify-content: center;
            background-image: url(bcim.jpg);
            background-size: cover;
            background-repeat: repeat;
            backdrop-filter: blur(5px);
        }
        nav {
            padding: 0px;
            margin: 0px;
        }
        .navbar {
            font-size: 30px;
            border-radius: 5px;
            padding-right: 15px;
            padding-left: 15px;
            background-color: rgb(0, 0, 0);
        }
        .navdiv {
            position: sticky;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo a {
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 10px;
            word-spacing: 15px;
        }
        li {
            font-size: 20px;
            display: inline-block;
            border-color: white;
            letter-spacing: 2px;
        }
        li a {
            text-decoration: none;
            border-radius: 10px;
            border-color: rgb(247, 0, 0);
            height: 27px;
        }
        li a:hover {
            border: none;
            box-shadow: 0px 0px 15px 2px rgb(65, 235, 19);
            border-radius: 5px;
            background: transparent;
        }
        .file-item {
            margin: 15px;
            text-align: center;
        }
        .download-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .download-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navdiv">
            <div class="logo"><a href="EDTHome.html"><font color="solid orange">STUDENT DOCK</font></a></div>
            <ul>
                <input type="search" id="search" placeholder="Search here" onkeyup="filterFiles()">
                <li><a href="EDTHome.html"><font color="solid orange">Home</font></a></li>
            </ul>
        </div>
    </nav>
    <h2>Available Files for Download:</h2>
    <div id="fileContainer" style='display: flex; flex-wrap: wrap;'> <!-- Container for file previews -->
        <?php
        $dir = "uploads/"; // Specify the directory where files are stored

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != "..") {
                        $filePath = $dir . $file;
                        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);

                        echo "<div class='file-item' data-file-name='" . strtolower($file) . "'>";

                        // Display previews for specific file types
                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . $filePath . "' alt='" . $file . "' style='width: 350px; height: auto;'><br>";
                        } elseif ($fileExt === 'pdf') {
                            echo "<iframe src='" . $filePath . "' style='width:500px; height:350px;'></iframe><br>";
                        } else {
                            echo "<p>No preview available for " . $file . "</p>";
                        }

                        // Add download link
                        echo "<a href='" . $filePath . "' download class='download-btn'>Download " . $file . "</a>";
                        echo "</div>";
                    }
                }
                closedir($dh);
            }
        } else {
            echo "The directory does not exist.";
        }
        ?>
    </div>

    <script>
        function filterFiles() {
            var input = document.getElementById('search');
            var filter = input.value.toLowerCase();
            var fileContainer = document.getElementById('fileContainer');
            var fileItems = fileContainer.getElementsByClassName('file-item');

            for (var i = 0; i < fileItems.length; i++) {
                var fileName = fileItems[i].getAttribute('data-file-name');
                if (fileName.indexOf(filter) > -1) {
                    fileItems[i].style.display = "";
                } else {
                    fileItems[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
