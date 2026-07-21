<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalAlumni = $result->fetch_assoc()['total'];

// Total Posts
$result = $conn->query("SELECT COUNT(*) AS total FROM posts");
$totalPosts = $result->fetch_assoc()['total'];

// Total Jobs
$result = $conn->query("SELECT COUNT(*) AS total FROM jobs");
$totalJobs = $result->fetch_assoc()['total'];

// Total Accepted Connections
$result = $conn->query("
SELECT COUNT(*) AS total
FROM connections
WHERE (sender_id=$user_id OR receiver_id=$user_id)
AND status='Accepted'
");
$totalConnections = $result->fetch_assoc()['total'];


$notificationResult = $conn->query("
SELECT COUNT(*) AS total
FROM notifications
WHERE receiver_id = $user_id
AND status = 'Unread'
");

$notificationCount = $notificationResult->fetch_assoc()['total'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Alumni Networking System</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<div class="sidebar">

<h2>Alumni System</h2>

<a href="dashboard.php">
<i class="fa fa-home"></i> Dashboard
</a>

<a href="profile.php">
<i class="fa fa-user"></i> My Profile
</a>

<a href="directory.php">
<i class="fa fa-users"></i> Alumni Directory
</a>

<a href="requests.php">
<i class="fa fa-user-plus"></i> Connection Requests
</a>

<a href="notifications.php">
<i class="fa fa-bell"></i>
Notifications
<?php if($notificationCount>0){ ?>
(<?php echo $notificationCount; ?>)
<?php } ?>
</a>

<a href="messages.php">
<i class="fa fa-envelope"></i> Messages
</a>

<a href="jobs.php">
<i class="fa fa-briefcase"></i> Job Board
</a>

<a href="posts.php">
<i class="fa fa-newspaper"></i> Posts
</a>

<a href="logout.php">
<i class="fa fa-right-from-bracket"></i> Logout
</a>

</div>

<div class="main">

<div class="header">

<div>

<h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?> 👋</h1>

<p>Alumni Networking System Dashboard</p>

</div>

<a class="logout" href="logout.php">Logout</a>

</div>

<div class="cards">

<div class="card">
<h2><?php echo $totalAlumni; ?></h2>
<p>Total Alumni</p>
</div>

<div class="card">
<h2><?php echo $totalPosts; ?></h2>
<p>Total Posts</p>
</div>

<div class="card">
<h2><?php echo $totalJobs; ?></h2>
<p>Total Jobs</p>
</div>

<div class="card">
<h2><?php echo $totalConnections; ?></h2>
<p>Connections</p>
</div>



</div>

<div class="profile">

<div style="margin-bottom:20px;">

<h2 style="
font-size:28px;
font-weight:700;
color:#222;
display:block;
margin-bottom:20px;
">
My Information
</h2>

<div style="text-align:center;">

<?php
$profileImage = !empty($user['profile_picture'])
    ? "uploads/" . htmlspecialchars($user['profile_picture'])
    : "images/default-user.png";
?>

<img src="<?php echo $profileImage; ?>"
style="
width:120px;
height:120px;
border-radius:50%;
object-fit:cover;
border:3px solid #0d6efd;
">

</div>

</div>
<table>

<tr>
<td><b>Full Name</b></td>
<td><?php echo htmlspecialchars($user['full_name']); ?></td>
</tr>

<tr>
<td><b>Email</b></td>
<td><?php echo htmlspecialchars($user['email']); ?></td>
</tr>

<tr>
<td><b>Department</b></td>
<td><?php echo htmlspecialchars($user['department']); ?></td>
</tr>

<tr>
<td><b>Graduation Year</b></td>
<td><?php echo htmlspecialchars($user['graduation_year']); ?></td>
</tr>

<tr>
<td><b>Phone</b></td>
<td><?php echo htmlspecialchars($user['phone']); ?></td>
</tr>

</table>

</div>

</body>
</html>
