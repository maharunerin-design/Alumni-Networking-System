<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['post'])) {

    $content = trim($_POST['content']);

    if (!empty($content)) {

        $stmt = $conn->prepare("INSERT INTO posts(user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $stmt->close();

    }

    header("Location: posts.php");
    exit();
}

$posts = $conn->query("
SELECT posts.*, users.full_name
FROM posts
JOIN users ON users.id = posts.user_id
ORDER BY posts.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Posts</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Poppins,sans-serif;
margin:0;
}

.container{
width:80%;
margin:40px auto;
}

.card{
background:#fff;
padding:25px;
border-radius:12px;
margin-bottom:20px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

textarea{
width:100%;
padding:15px;
border:1px solid #ddd;
border-radius:8px;
resize:none;
box-sizing:border-box;
}

button{
margin-top:10px;
padding:10px 20px;
background:#0d6efd;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
}

.post{
padding:20px;
border-bottom:1px solid #ddd;
}

.action a{
margin-right:15px;
text-decoration:none;
font-weight:bold;
}

.comment-box{
margin-top:15px;
}

.comment{
background:#f5f5f5;
padding:10px;
margin-top:10px;
border-radius:8px;
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

<h2>Create New Post</h2>

<form method="POST">

<textarea
name="content"
rows="5"
placeholder="Share something with Alumni..."
required></textarea>

<button type="submit" name="post">Post</button>

</form>

</div>

<div class="card">

<h2>Recent Posts</h2>

<?php

if($posts->num_rows==0){
    echo "<p>No posts available.</p>";
}

while($row=$posts->fetch_assoc()){

$like=$conn->query("SELECT COUNT(*) AS total FROM post_likes WHERE post_id=".$row['id']);
$likeCount=$like->fetch_assoc()['total'];

?>

<div class="post">

<h3><?php echo htmlspecialchars($row['full_name']); ?></h3>

<p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

<small><?php echo $row['created_at']; ?></small>

<div class="action">

<a href="like_post.php?id=<?php echo $row['id']; ?>">
❤️ Like (<?php echo $likeCount; ?>)
</a>

<?php if($row['user_id']==$_SESSION['user_id']){ ?>

<a href="edit_post.php?id=<?php echo $row['id']; ?>">
✏️ Edit
</a>

<a href="delete_post.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this post?')"
style="color:red;">
🗑 Delete
</a>

<?php } ?>

</div>

<div class="comment-box">

<form action="add_comment.php" method="POST">

<input
type="hidden"
name="post_id"
value="<?php echo $row['id']; ?>">

<input
type="text"
name="comment"
placeholder="Write a comment..."
required
style="width:75%;padding:8px;">

<button type="submit">
Comment
</button>

</form>

<?php

$comments=$conn->query("
SELECT comments.*, users.full_name
FROM comments
JOIN users
ON users.id=comments.user_id
WHERE comments.post_id=".$row['id']."
ORDER BY comments.id DESC
");

while($c=$comments->fetch_assoc()){

?>

<div class="comment">

<b><?php echo htmlspecialchars($c['full_name']); ?></b><br>

<?php echo nl2br(htmlspecialchars($c['comment'])); ?>

<br>

<small><?php echo $c['created_at']; ?></small>

<?php if($c['user_id']==$_SESSION['user_id']){ ?>

|

<a
href="delete_comment.php?id=<?php echo $c['id']; ?>"
onclick="return confirm('Delete this comment?')"
style="color:red;text-decoration:none;">
Delete
</a>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

<?php } ?>

</div>

<a class="back" href="dashboard.php">
⬅ Back to Dashboard
</a>

</div>

</body>
</html>