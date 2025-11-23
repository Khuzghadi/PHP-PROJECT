<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "NZ_IMS";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("Sorry to Connect to DB ".mysqli_connect_error());
}

?>

