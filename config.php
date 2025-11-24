<?php
$conn = new mysqli("localhost", "root", "", "nazafat_ims");
if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}
?>
