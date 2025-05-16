<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $course = $_POST['course'];
    $session = $_POST['session'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    
    // Handle remember me checkbox
    if (isset($_POST['remember'])) {
        setcookie('remembered_email', $email, time() + (30 * 24 * 60 * 60)); // 30 days
    } else {
        setcookie('remembered_email', '', time() - 3600); // Delete cookie
    }
    
    // Handle file upload
    $targetDir = "uploads/";

// Create the uploads directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$imagePath = "";

// Check if a file was submitted
if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES["image"]["tmp_name"];
    $fileName = basename($_FILES["image"]["name"]);
    $fileSize = $_FILES["image"]["size"];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file is an actual image
    $check = getimagesize($fileTmpPath);
    if ($check === false) {
        die("File is not an image.");
    }

    // Allowed file types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    // Limit file size to 2MB
    if ($fileSize > 2 * 1024 * 1024) {
        die("File is too large. Max allowed size is 2MB.");
    }

    // Create a unique filename to avoid collisions
    $newFileName = uniqid("img_", true) . "." . $fileType;
    $targetFile = $targetDir . $newFileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($fileTmpPath, $targetFile)) {
        $imagePath = $targetFile;
        echo "Upload successful! File stored at: " . htmlspecialchars($imagePath);
    } else {
        die("There was an error moving the uploaded file.");
    }
} else {
    die("No image file was uploaded or an error occurred.");
}
    
    // Prepare and execute SQL
    $sql = "INSERT INTO registrations (first_name, last_name, email, password, course, session, gender, phone, image_path, registration_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $password, $course, $session, $gender, $phone, $imagePath);
    
    if ($stmt->execute()) {
        header("Location: view.php"); // Redirect to view page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>