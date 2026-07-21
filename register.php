<?php
session_start();
require_once "includes/db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $graduation_year = trim($_POST['graduation_year']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (
        empty($full_name) ||
        empty($student_id) ||
        empty($email) ||
        empty($department) ||
        empty($graduation_year) ||
        empty($phone) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email address.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $error = "Email already exists.";

        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users
                (full_name, student_id, email, password, department, graduation_year, phone)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssssis",
                $full_name,
                $student_id,
                $email,
                $hashedPassword,
                $department,
                $graduation_year,
                $phone
            );

            if ($stmt->execute()) {

                $_SESSION['success'] = "Registration Successful. Please Login.";
                header("Location: login.php");
                exit();

            } else {

                $error = "Registration Failed : " . $conn->error;

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Alumni Registration</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <div class="register-card">

        <div class="logo">
            <h1>Alumni Networking System</h1>
            <p>Create Your Alumni Account</p>
        </div>

        <?php if($error != ""){ ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="" method="POST" id="registerForm">

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter Full Name" required>
            </div>

            <div class="input-group">
                <label>Student ID</label>
                <input type="text" name="student_id" placeholder="Enter Student ID" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>

            <div class="input-group">
                <label>Department</label>
                <input type="text" name="department" placeholder="Enter Department" required>
            </div>

            <div class="input-group">
                <label>Graduation Year</label>
                <input type="number" name="graduation_year" min="1990" max="2050" placeholder="2026" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="01XXXXXXXXX" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn-register">
                <i class="fa-solid fa-user-plus"></i> Register
            </button>

        </form>

        <div class="login-link">
            <p>
                Already have an account?
                <a href="login.php">Login Here</a>
            </p>
        </div>

    </div>

</div>

<script src="js/validation.js"></script>

</body>
</html>