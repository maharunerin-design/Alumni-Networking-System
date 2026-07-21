<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (isset($_POST['update'])) {
    $profile_picture = $user['profile_picture'];


if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error']==0){

    $fileName = time()."_".basename($_FILES['profile_picture']['name']);
    $target = "uploads/".$fileName;

    move_uploaded_file($_FILES['profile_picture']['tmp_name'],$target);

    $profile_picture = $fileName;
}
    $full_name = trim($_POST['full_name']);
    $department = trim($_POST['department']);
    $graduation_year = trim($_POST['graduation_year']);
    $phone = trim($_POST['phone']);
    $profession = trim($_POST['profession']);
    $company = trim($_POST['company']);
    $bio = trim($_POST['bio']);

    $stmt = $conn->prepare("
UPDATE users
SET
full_name=?,
department=?,
graduation_year=?,
phone=?,
profession=?,
company=?,
bio=?,
profile_picture=?
WHERE id=?
");

   $stmt->bind_param(
    "ssisssssi",
    $full_name,
    $department,
    $graduation_year,
    $phone,
    $profession,
    $company,
    $bio,
    $profile_picture,
    $user_id
);

    if($stmt->execute()){

    echo "Profile Updated Successfully";
    exit();

}else{

    die("SQL Error: ".$stmt->error);

}
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>My Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:#eef2f7;
}

.container{

width:700px;
margin:40px auto;
background:white;
padding:30px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,.1);

}

input,textarea{

width:100%;
padding:12px;
margin:8px 0 18px;
border:1px solid #ddd;
border-radius:8px;

}

button{

background:#0d6efd;
color:white;
border:none;
padding:14px;
width:100%;
border-radius:8px;
cursor:pointer;

}

button:hover{

background:#084298;

}

a{

text-decoration:none;

}

</style>

</head>

<body>

<div class="container">

<h2>My Profile</h2>

<form method="POST" enctype="multipart/form-data">


<div style="text-align:center;margin-bottom:25px;">

<?php
$image = !empty($user['profile_picture'])
    ? "uploads/" . htmlspecialchars($user['profile_picture'])
    : "images/default-user.png";
?>

<img
src="<?php echo $image; ?>"
style="
width:150px;
height:150px;
border-radius:50%;
object-fit:cover;
border:4px solid #0d6efd;
margin-bottom:15px;
">

<br>

<label style="font-weight:600;">Profile Picture</label>

<input
type="file"
name="profile_picture"
accept=".jpg,.jpeg,.png,.gif,.webp"
style="margin-top:10px;">

</div>

<label>Full Name</label>
<input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">

<label>Department</label>
<input type="text" name="department" value="<?php echo htmlspecialchars($user['department']); ?>">

<label>Graduation Year</label>
<input type="number" name="graduation_year" value="<?php echo htmlspecialchars($user['graduation_year']); ?>">

<label>Phone</label>
<input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

<label>Profession</label>
<input type="text" name="profession" value="<?php echo htmlspecialchars($user['profession']); ?>">

<label>Company</label>
<input type="text" name="company" value="<?php echo htmlspecialchars($user['company']); ?>">

<label>Bio</label>
<textarea name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>

<button name="update">Update Profile</button>

</form>

<br>

<a href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>