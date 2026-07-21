<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$requests = $conn->query("
SELECT
connections.id,
users.full_name,
users.department,
users.graduation_year,
users.email
FROM connections
JOIN users
ON users.id = connections.sender_id
WHERE connections.receiver_id = $user_id
AND connections.status='Pending'
ORDER BY connections.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Connection Requests</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Poppins,sans-serif;
}

.container{
width:80%;
margin:40px auto;
}

.card{
background:#fff;
padding:25px;
margin-bottom:20px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

h2{
margin-bottom:20px;
color:#0d6efd;
}

.btn{
display:inline-block;
padding:10px 18px;
border-radius:6px;
text-decoration:none;
color:#fff;
margin-right:10px;
font-weight:bold;
}

.accept{
background:#198754;
}

.reject{
background:#dc3545;
}

.back{
display:inline-block;
margin-top:20px;
padding:10px 20px;
background:#0d6efd;
color:#fff;
text-decoration:none;
border-radius:8px;
}

</style>

</head>

<body>

<div class="container">

<h2>Connection Requests</h2>

<?php

if($requests->num_rows==0){

echo "<p>No pending requests.</p>";

}

while($row=$requests->fetch_assoc()){

?>

<div class="card">

<h3><?php echo htmlspecialchars($row['full_name']); ?></h3>

<p>

Department :
<?php echo htmlspecialchars($row['department']); ?>

<br><br>

Graduation :
<?php echo htmlspecialchars($row['graduation_year']); ?>

<br><br>

Email :
<?php echo htmlspecialchars($row['email']); ?>

</p>

<a class="btn accept"
href="accept_request.php?id=<?php echo $row['id']; ?>">
Accept
</a>

<a class="btn reject"
href="reject_request.php?id=<?php echo $row['id']; ?>">
Reject
</a>

</div>

<?php } ?>

<a class="back" href="dashboard.php">
⬅ Back to Dashboard
</a>

</div>

</body>
</html>