<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: my_submissions.php");
    }
    exit();
}

$conn = new mysqli("localhost", "root", "", "infohub_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Admin boleh delete any post, user only their own pending posts
if ($role == 'admin') {
    $sql = "DELETE FROM post WHERE Post_ID='$post_id'";
    $redirect = "admin.php?deleted=1";
} else {
    $sql = "DELETE FROM post WHERE Post_ID='$post_id' AND User_ID='$user_id' AND status='pending'";
    $redirect = "my_submissions.php?deleted=1";
}

if ($conn->query($sql) === TRUE) {
    header("Location: $redirect");
} else {
    echo "Error deleting record: " . $conn->error;
}

exit();
?>