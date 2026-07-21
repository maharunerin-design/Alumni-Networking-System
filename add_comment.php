<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['comment'])) {

    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);

    if ($comment != "") {

        $stmt = $conn->prepare("INSERT INTO comments(post_id,user_id,comment) VALUES(?,?,?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
        $stmt->execute();

    }
}

header("Location: posts.php");
exit();