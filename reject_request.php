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
SET status='Rejected'
WHERE id=?
AND receiver_id=?
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: requests.php");
exit();
?>
