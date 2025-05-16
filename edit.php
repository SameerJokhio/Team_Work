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

// Fetch student data
$student = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM registrations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $session = $_POST['session'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];

    $sql = "UPDATE registrations SET 
                first_name = ?, 
                last_name = ?, 
                email = ?, 
                course = ?, 
                session = ?, 
                gender = ?, 
                phone = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $firstName, $lastName, $email, $course, $session, $gender, $phone, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Error updating record');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Student Record</h2>
        <?php if ($student): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Course</label>
                <select name="course" required>
                    <option value="Computer Science" <?php if ($student['course'] == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                    <option value="Engineering" <?php if ($student['course'] == 'Engineering') echo 'selected'; ?>>Engineering</option>
                    <option value="Business Administration" <?php if ($student['course'] == 'Business Administration') echo 'selected'; ?>>Business Administration</option>
                    <option value="Medicine" <?php if ($student['course'] == 'Medicine') echo 'selected'; ?>>Medicine</option>
                    <option value="Law" <?php if ($student['course'] == 'Law') echo 'selected'; ?>>Law</option>
                </select>
            </div>
            <div class="form-group">
                <label>Session</label>
                <select name="session" required>
                    <option value="Morning" <?php if ($student['session'] == 'Morning') echo 'selected'; ?>>Morning</option>
                    <option value="Afternoon" <?php if ($student['session'] == 'Afternoon') echo 'selected'; ?>>Afternoon</option>
                    <option value="Evening" <?php if ($student['session'] == 'Evening') echo 'selected'; ?>>Evening</option>
                </select>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
            </div>
            <button type="submit">Update</button>
        </form>
        <?php else: ?>
            <p style="text-align: center;">Student record not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
