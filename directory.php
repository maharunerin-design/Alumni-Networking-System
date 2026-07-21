<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {

    $search = trim($_GET['search']);

    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE full_name LIKE ?
        OR department LIKE ?
        OR graduation_year LIKE ?
        ORDER BY full_name ASC
    ");

    $like = "%".$search."%";

    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT *
        FROM users
        ORDER BY full_name ASC
    ");

}

// Build a map of this user's connections (other_user_id => status)
$user_id = $_SESSION['user_id'];
$connMap = [];

$connResult = $conn->query("
    SELECT sender_id, receiver_id, status
    FROM connections
    WHERE sender_id=$user_id OR receiver_id=$user_id
");

while($c = $connResult->fetch_assoc()){
    $otherId = ($c['sender_id'] == $user_id) ? $c['receiver_id'] : $c['sender_id'];
    $connMap[$otherId] = $c['status'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Alumni Directory</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
}

body{
background:#eef2f7;
}

.container{
width:90%;
margin:40px auto;
}

h2{
margin-bottom:20px;
}

.search-box{
display:flex;
gap:10px;
margin-bottom:25px;
}

.search-box input{
flex:1;
padding:12px;
border:1px solid #ccc;
border-radius:8px;
}

.search-box button{
padding:12px 20px;
border:none;
background:#0d6efd;
color:white;
border-radius:8px;
cursor:pointer;
}

table{
width:100%;
background:white;
border-collapse:collapse;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

th{
background:#0d6efd;
color:white;
padding:15px;
}

td{
padding:15px;
border-bottom:1px solid #eee;
text-align:center;
}

tr:hover{
background:#f8f9fa;
}

.back{
display:inline-block;
margin-top:25px;
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

<h2>Alumni Directory</h2>

<form method="GET">

<div class="search-box">

<input
type="text"
name="search"
placeholder="Search by Name, Department or Graduation Year"
value="<?php echo htmlspecialchars($search); ?>">

<button type="submit">Search</button>
</div>

</form>

<table>

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Department</th>
    <th>Graduation Year</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

    <td><?php echo $row['id']; ?></td>

    <td><?php echo htmlspecialchars($row['full_name']); ?></td>

    <td><?php echo htmlspecialchars($row['department']); ?></td>

    <td><?php echo htmlspecialchars($row['graduation_year']); ?></td>

    <td><?php echo htmlspecialchars($row['email']); ?></td>

    <td><?php echo htmlspecialchars($row['phone']); ?></td>

    <td>

    <?php if($row['id'] != $_SESSION['user_id']){

        $status = isset($connMap[$row['id']]) ? $connMap[$row['id']] : null;

        if($status == 'Accepted'){ ?>

            <a href="chat.php?id=<?php echo $row['id']; ?>"
            style="background:#198754;color:white;padding:8px 15px;border-radius:6px;text-decoration:none;">
            💬 Message
            </a>

        <?php } elseif($status == 'Pending'){ ?>

            <span style="color:#888;">Pending...</span>

        <?php } else { ?>

            <a href="send_request.php?id=<?php echo $row['id']; ?>"
            style="background:#0d6efd;color:white;padding:8px 15px;border-radius:6px;text-decoration:none;">
            Connect
            </a>

        <?php }

    } else { ?>

        -

    <?php } ?>

    </td>

</tr>

<?php } ?>

</table>
<a class="back" href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>