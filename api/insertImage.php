<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file'])) {
        $targetDirectory = 'uploads/';
        $targetFile = $targetDirectory . basename($_FILES['file']['name']);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo json_encode(['status' => 'error', 'message' => 'File already exists.']);
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES['file']['size'] > 5000000) {
            echo json_encode(['status' => 'error', 'message' => 'File is too large.']);
            $uploadOk = 0;
        }

        // Allow certain file formats (optional)
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, GIF, and PDF files are allowed.']);
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Your file was not uploaded.']);
        } else {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                echo json_encode(['status' => 'success', 'message' => 'The file ' . basename($_FILES['file']['name']) . ' has been uploaded.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'There was an error uploading your file.']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file was uploaded.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>