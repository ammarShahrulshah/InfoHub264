<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/printing/db_conn/db_conn.php';

$id = $_SESSION['id'];
$cat = $_SESSION['categories'];

if ($cat == "A") {
    $tables = "admin";
} else {
    $tables = "users";
}

$mysql = "SELECT * FROM $tables WHERE login_id='$id'";
$result = mysqli_query($conn, $mysql);
$row = mysqli_fetch_array($result);

$nama = $row['nama'];
$id = $row['login_id'];

if(!isset($id)) {
    header("Location: index.php");
}
?>