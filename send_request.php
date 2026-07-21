<?php
session_start();
require_once "includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$sender = $_SESSION['user_id'];
$receiver = (int)$_GET['id'];

if($sender != $receiver){

    $check = $conn->prepare("
    SELECT id
    FROM connections
    WHERE sender_id=? AND receiver_id=?
    ");

    $check->bind_param("ii",$sender,$receiver);
    $check->execute();

    $message = "sent you a connection request.";

    $result = $check->get_result();

    if($result->num_rows==0){

        $stmt = $conn->prepare("
        INSERT INTO connections(sender_id,receiver_id)
        VALUES(?,?)
        ");

        $stmt->bind_param("ii",$sender,$receiver);
        $stmt->execute();

        $notify = $conn->prepare("
        INSERT INTO notifications(sender_id, receiver_id, message)
        VALUES(?, ?, ?)
        ");

        $notify->bind_param("iis", $sender, $receiver, $message);
        $notify->execute();

    }

}

header("Location: directory.php");
exit();