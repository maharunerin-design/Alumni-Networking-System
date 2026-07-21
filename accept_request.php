<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];

$stmt = $conn->prepare("
UPDATE connections
SET status='Accepted'
WHERE id=?
AND receiver_id=?
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$get = $conn->prepare("
SELECT sender_id
FROM connections
WHERE id=?
");

$get->bind_param("i", $id);
$get->execute();

$result = $get->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    $sender = $row['sender_id'];

    $message = "accepted your connection request.";

    $notify = $conn->prepare("
    INSERT INTO notifications(sender_id,receiver_id,message)
    VALUES(?,?,?)
    ");

    $notify->bind_param("iis", $user_id, $sender, $message);
    $notify->execute();
}

header("Location: requests.php");
exit();
?>