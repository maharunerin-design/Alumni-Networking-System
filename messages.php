<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Send Message
if(isset($_POST['send'])){

    $receiver = $_POST['receiver'];
    $message = trim($_POST['message']);

    if($message!=""){

        $stmt=$conn->prepare("INSERT INTO messages(sender_id,receiver_id,message) VALUES(?,?,?)");

        $stmt->bind_param("iis",$user_id,$receiver,$message);

        $stmt->execute();

    }

}

// All Users except Me
$users=$conn->query("SELECT id,full_name FROM users WHERE id!=$user_id");

// Inbox
$stmt=$conn->prepare("
SELECT
m.message,
m.sent_at,
u.full_name
FROM messages m
JOIN users u
ON u.id=m.sender_id
WHERE receiver_id=?
ORDER BY m.id DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$messages=$stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Messages</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:#eef2f7;
}

.container{

width:80%;
margin:40px auto;

}

.card{

background:white;
padding:25px;
border-radius:12px;
margin-bottom:25px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}

select,
textarea{

width:100%;
padding:12px;
margin:10px 0;
border:1px solid #ddd;
border-radius:8px;

}

button{

padding:12px 20px;
background:#0d6efd;
color:white;
border:none;
border-radius:8px;
cursor:pointer;

}

.message{

padding:15px;
border-bottom:1px solid #eee;

}

.back{

display:inline-block;
margin-top:20px;
background:#0d6efd;
color:white;
padding:10px 20px;
text-decoration:none;
border-radius:8px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>Send Message</h2>

<form method="POST">

<select name="receiver" required>

<option value="">Select Alumni</option>

<?php while($u=$users->fetch_assoc()){ ?>

<option value="<?php echo $u['id']; ?>">

<?php echo htmlspecialchars($u['full_name']); ?>

</option>

<?php } ?>

</select>

<textarea
name="message"
rows="5"
placeholder="Write your message..."
required></textarea>

<button name="send">Send Message</button>

</form>

</div>

<div class="card">

<h2>Inbox</h2>

<?php

if($messages->num_rows==0){

echo "No messages.";

}

while($msg=$messages->fetch_assoc()){

?>

<div class="message">

<b><?php echo htmlspecialchars($msg['full_name']); ?></b>

<br><br>

<?php echo nl2br(htmlspecialchars($msg['message'])); ?>

<br><br>

<small>

<?php echo $msg['sent_at']; ?>

</small>

</div>

<?php } ?>

</div>

<a class="back" href="dashboard.php">

⬅ Back to Dashboard

</a>
<a href="jobs.php"><i class="fa fa-briefcase"></i> Job Board</a>

</div>

</body>
</html>