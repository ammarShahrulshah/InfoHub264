<?php
include 'db_conn.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if($result->num_rows > 0){

    $user = $result->fetch_assoc();

    if(password_verify($password,$user['password'])){
        header("Location: home.html");
    }else{
        echo "Wrong password";
    }

}else{
    echo "User not found";
}
?>