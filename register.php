<?php
include 'db_conn.php';

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];

$password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users(fullname,email,password)
VALUES('$fullname','$email','$password')";

if($conn->query($sql)){
    echo "Success";
}else{
    echo "Error";
}
?>