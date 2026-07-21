<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark notifications as read
$conn->query("UPDATE notifications SET status='Read' WHERE receiver_id=$user_id");

// Get notifications
$notifications = $conn->query("
SELECT notifications.*, users.full_name
FROM notifications
JOIN users ON users.id = notifications.sender_id
WHERE notifications.receiver_id = $user_id
ORDER BY notifications.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Notifications</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
}

body{
background:#eef2f7;
}

.container{
width:80%;
margin:40px auto;
}

.card{
background:#fff;
padding:30px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.card h2{
color:#0d6efd;
margin-bottom:20px;
}

.item{
background:#f8f9fa;
border-left:5px solid #0d6efd;
padding:18px;
margin-bottom:15px;
border-radius:10px;
transition:.3s;
}

.item:hover{
background:#e9f2ff;
transform:translateX(5px);
}

.item b{
color:#0d6efd;
font-size:17px;
}

.item small{
display:block;
margin-top:8px;
color:#666;
}

.back{
display:inline-block;
margin-top:20px;
padding:10px 20px;
background:#0d6efd;
color:white;
text-decoration:none;
border-radius:8px;
}

.back:hover{
background:#0b5ed7;
}

</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>🔔 Notifications</h2>

<?php if($notifications->num_rows==0){ ?>

<p>No notifications found.</p>

<?php } ?>

<?php while($row = $notifications->fetch_assoc()){ ?>

<div class="item">

<div style="font-size:18px;">
🔔 <b><?php echo htmlspecialchars($row['full_name']); ?></b>
<?php echo htmlspecialchars($row['message']); ?>
</div>

<small>
🕒 <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
</small>

</div>

<?php } ?>

</div>

<a class="back" href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>