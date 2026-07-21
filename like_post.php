<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_GET['id'];

$check = $conn->prepare("SELECT id FROM post_likes WHERE post_id=? AND user_id=?");
$check->bind_param("ii", $post_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {

    $delete = $conn->prepare("DELETE FROM post_likes WHERE post_id=? AND user_id=?");
    $delete->bind_param("ii", $post_id, $user_id);
    $delete->execute();

} else {

    $insert = $conn->prepare("INSERT INTO post_likes(post_id,user_id) VALUES(?,?)");
    $insert->bind_param("ii", $post_id, $user_id);
    $insert->execute();

}

header("Location: posts.php");
exit();
?>