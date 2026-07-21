<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {

    $post_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    // শুধু নিজের পোস্ট ডিলিট করতে পারবে
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);

    if ($stmt->execute()) {
        header("Location: posts.php");
        exit();
    } else {
        echo "Failed to delete post.";
    }
} else {
    header("Location: posts.php");
    exit();
}
?>