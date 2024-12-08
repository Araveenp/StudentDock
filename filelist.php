<?php
$dir = "uploads/"; // Specify the directory where files are stored
$filesList = [];

if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != "..") {
                $filePath = $dir . $file;
                $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
                $fileInfo = [
                    'name' => $file,
                    'path' => $filePath,
                    'extension' => $fileExt,
                ];
                $filesList[] = $fileInfo;
            }
        }
        closedir($dh);
    }
}

header('Content-Type: application/json');
echo json_encode($filesList);
?>
