<?php
session_start();
require_once "includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header("Location: posts.php");
    exit();
}

$post_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii",$post_id,$user_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if(!$post){
    die("Post not found.");
}

if(isset($_POST['update'])){

    $content = trim($_POST['content']);

    $stmt = $conn->prepare("UPDATE posts SET content=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii",$content,$post_id,$user_id);
    $stmt->execute();

    header("Location: posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Post</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Poppins;
}

.box{
width:700px;
margin:60px auto;
background:white;
padding:30px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

textarea{
width:100%;
padding:15px;
border:1px solid #ddd;
border-radius:8px;
}

button{
margin-top:15px;
padding:12px 20px;
background:#0d6efd;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
}

</style>

</head>

<body>

<div class="box">

<h2>Edit Post</h2>

<form method="POST">

<textarea name="content" rows="6"><?php echo htmlspecialchars($post['content']); ?></textarea>

<br>

<button name="update">Update Post</button>

</form>

</div>

</body>

</html>