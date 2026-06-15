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

$conn->query("UPDATE post SET status='$status' WHERE Post_ID='$post_id'");

// Redirect back to admin.php with success message
header("Location: admin.php?msg=Post+successfully+".$status);
exit();
?>