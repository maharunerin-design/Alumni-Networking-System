<?php
session_start();
require_once "includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$sender = $_SESSION['user_id'];
$receiver = (int)$_GET['id'];

// Only allow chat between users who are connected (Accepted)
$connCheck = $conn->prepare("
SELECT status FROM connections
WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
");
$connCheck->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$connCheck->execute();
$connRow = $connCheck->get_result()->fetch_assoc();

if(!$connRow || $connRow['status'] != 'Accepted'){
    header("Location: directory.php");
    exit();
}

// Send Message
if(isset($_POST['send'])){

    $message = trim($_POST['message']);

    if($message != ""){

        $stmt = $conn->prepare("
        INSERT INTO messages(sender_id, receiver_id, message)
        VALUES(?,?,?)
        ");

        $stmt->bind_param("iis",$sender,$receiver,$message);
        $stmt->execute();

        header("Location: chat.php?id=".$receiver);
        exit();
    }
}

// Receiver Info
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id=?");
$stmt->bind_param("i",$receiver);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Chat History
$chat = $conn->query("
SELECT messages.*, users.full_name
FROM messages
JOIN users ON users.id = messages.sender_id
WHERE
(sender_id=$sender AND receiver_id=$receiver)
OR
(sender_id=$receiver AND receiver_id=$sender)
ORDER BY id ASC
");
?>
<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Chat</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Poppins,sans-serif;
}

.container{
width:70%;
margin:30px auto;
}

.chat-box{
background:#fff;
padding:20px;
border-radius:12px;
height:500px;
overflow-y:auto;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.me{
background:#0d6efd;
color:#fff;
padding:10px 15px;
border-radius:12px;
margin:10px 0;
width:fit-content;
margin-left:auto;
max-width:70%;
}

.other{
background:#f1f1f1;
padding:10px 15px;
border-radius:12px;
margin:10px 0;
width:fit-content;
max-width:70%;
}

form{
display:flex;
margin-top:15px;
gap:10px;
}

input{
flex:1;
padding:12px;
border:1px solid #ccc;
border-radius:8px;
}

button{
padding:12px 20px;
background:#0d6efd;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
}

.back{
display:inline-block;
margin-top:20px;
padding:10px 20px;
background:#198754;
color:white;
text-decoration:none;
border-radius:8px;
}

</style>



</head>

<body>

<body>

<div class="container">

<h2>
💬 Chat with
<?php echo htmlspecialchars($user['full_name']); ?>
</h2>

<div class="chat-box">

<?php while($row = $chat->fetch_assoc()){ ?>

<?php if($row['sender_id'] == $sender){ ?>

<div class="me">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

<br>

<small>
<?php echo date("d M h:i A", strtotime($row['sent_at'])); ?>
</small>

</div>

<?php } else { ?>

<div class="other">

<b><?php echo htmlspecialchars($row['full_name']); ?></b><br>

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

<br>

<small>
<?php echo date("d M h:i A", strtotime($row['sent_at'])); ?>
</small>

</div>

<?php } ?>

<?php } ?>

</div>

<form method="POST">

<input
type="text"
name="message"
placeholder="Type your message..."
required>

<button type="submit" name="send">
📤 Send
</button>

</form>

<br>

<a class="back" href="directory.php">
⬅ Back to Directory
</a>

</div>

</body>
</html>

</body>

</html>
