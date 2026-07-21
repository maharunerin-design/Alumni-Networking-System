<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['post_job'])){

    $title = trim($_POST['job_title']);
    $company = trim($_POST['company']);
    $description = trim($_POST['description']);

    if($title!="" && $company!="" && $description!=""){

        $stmt=$conn->prepare("INSERT INTO jobs(user_id,company,job_title,description)
        VALUES(?,?,?,?)");

        $stmt->bind_param(
            "isss",
            $user_id,
            $company,
            $title,
            $description
        );

        $stmt->execute();

    }

}

$jobs=$conn->query("
SELECT jobs.*,users.full_name
FROM jobs
JOIN users
ON users.id=jobs.user_id
ORDER BY jobs.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Job Board</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{

background:#eef2f7;
font-family:Poppins;

}

.container{

width:85%;
margin:40px auto;

}

.card{

background:white;
padding:25px;
border-radius:12px;
margin-bottom:25px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}

input,
textarea{

width:100%;
padding:12px;
margin:10px 0;
border:1px solid #ddd;
border-radius:8px;

}

button{

background:#0d6efd;
color:white;
padding:12px 20px;
border:none;
border-radius:8px;
cursor:pointer;

}

.job{

padding:20px;
border-bottom:1px solid #eee;

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

</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>Post New Job</h2>

<form method="POST">

<input
type="text"
name="job_title"
placeholder="Job Title"
required>

<input
type="text"
name="company"
placeholder="Company Name"
required>

<textarea
name="description"
rows="5"
placeholder="Job Description"
required></textarea>

<button name="post_job">

Post Job

</button>

</form>

</div>

<div class="card">

<h2>Available Jobs</h2>

<?php

if($jobs->num_rows==0){

echo "No Job Posted Yet.";

}

while($job=$jobs->fetch_assoc()){

?>

<div class="job">

<h3><?php echo htmlspecialchars($job['job_title']); ?></h3>

<b>

<?php echo htmlspecialchars($job['company']); ?>

</b>

<br><br>

<?php echo nl2br(htmlspecialchars($job['description'])); ?>

<br><br>

<small>

Posted By

<?php echo htmlspecialchars($job['full_name']); ?>

|

<?php echo $job['created_at']; ?>

</small>

</div>

<?php } ?>

</div>

<a class="back" href="dashboard.php">

⬅ Back to Dashboard

</a>

</div>

</body>
</html>
