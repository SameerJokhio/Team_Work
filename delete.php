<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // First, get the image path to delete the file if it exists
    $sql_select = "SELECT image_path FROM registrations WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $delete_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $row = $result->fetch_assoc();
    
    // Delete the record from database
    $sql_delete = "DELETE FROM registrations WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    
    if ($stmt_delete->execute()) {
        // If deletion was successful, delete the associated image file
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
        
        // Redirect back to view page with success message
        header("Location: view.php?delete_success=1");
        exit();
    } else {
        // Redirect back with error message
        header("Location: view.php?delete_error=1");
        exit();
    }
} else {
    // If no delete_id was provided, redirect back
    header("Location: view.php");
    exit();
}

$conn->close();
?>