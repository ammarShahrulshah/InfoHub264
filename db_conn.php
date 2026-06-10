<?php
$db_host = "localhost";
$db_user = "root";
$db_pwd  = "";
$db_name = "InfoHub_db";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>