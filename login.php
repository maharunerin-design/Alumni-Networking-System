<?php
session_start();
require_once "includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
        
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
}
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Email not found.";

        }

    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Alumni Networking System</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <div class="register-card">

        <div class="logo">

            <h1>Alumni Networking System</h1>
            <p>Login to Your Account</p>

        </div>

        <?php
        if(isset($_SESSION['success'])){
            echo "<div class='success-message'>".$_SESSION['success']."</div>";
            unset($_SESSION['success']);
        }
        ?>

        <?php if($error!=""){ ?>

            <div class="error-message">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <form action="" method="POST">

            <div class="input-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter Email"
                    required>

            </div>

            <div class="input-group">

    <label>Password</label>

    <div style="position:relative;">

        <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter Password"
            required
            style="width:100%;height:55px;padding:0 45px 0 15px;box-sizing:border-box;">

        <i
            class="fa-solid fa-eye"
            id="togglePassword"
            style="
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#666;
            "></i>

    </div>

</div>
                        <button type="submit" class="btn-register">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>

        </form>

        <div class="login-link">
            <p>
                Don't have an account?
                <a href="register.php">Register Here</a>
            </p>
        </div>

    </div>

</div>
<script>

const password = document.getElementById("password");
const toggle = document.getElementById("togglePassword");

toggle.addEventListener("click", function(){

    if(password.type==="password"){

        password.type="text";
        toggle.classList.remove("fa-eye");
        toggle.classList.add("fa-eye-slash");

    }else{

        password.type="password";
        toggle.classList.remove("fa-eye-slash");
        toggle.classList.add("fa-eye");

    }

});

</script>
</body>
</html>