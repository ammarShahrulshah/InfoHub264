<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: admin.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

$post_id = $_GET['id'];
$status = $_GET['status'];

// Update post status
$conn->query("UPDATE post SET status='$status' WHERE Post_ID='$post_id'");

// Admin ID
$admin_user_id = $_SESSION['user_id'];
$admin_result = $conn->query("SELECT Admin_ID FROM admin WHERE User_ID = '$admin_user_id'");
$admin_row = $admin_result->fetch_assoc();
$admin_id = $admin_row['Admin_ID'];

// User ID (the one who submitted the post)
$post_result = $conn->query("SELECT User_ID, title FROM post WHERE Post_ID = '$post_id'");
$post_row = $post_result->fetch_assoc();
$user_id = $post_row['User_ID'];
$post_title = $post_row['title'];

$action = "Admin " . $status . " post";
$details = "Post: '" . $post_title . "' was " . $status;

// Insert with user_ID
$stmt = $conn->prepare("INSERT INTO activity_log (action, admin_ID, Post_ID, user_ID, details) 
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("siiis", $action, $admin_id, $post_id, $user_id, $details);
$stmt->execute();
$stmt->close();

header("Location: admin.php?msg=Post+successfully+".$status);
exit();
?>