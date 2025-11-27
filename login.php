<?php
session_start();
include "config.php";

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$user' or email='$user'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if($pass == $row['password']){
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['zone_name'] = $row['zone_name'];
            if($_SESSION['role'] == 'admin'){
                header("Location: dashboard.php");
                $conn->query("UPDATE users SET force_logout = 0 WHERE user_id = " . $row['user_id']);

            }else{
                header("Location: user_dashboard.php");
                $conn->query("UPDATE users SET force_logout = 0 WHERE user_id = " . $row['user_id']);

            }
            
        }else{
            echo "Password incorrect";
        }
    } else {
        echo "User Not Found!";
    }
}
?>


