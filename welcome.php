<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: signup.php');
    exit();
}
echo "Welcome, " . $_SESSION['name'] . "<br>";
echo "<img src='" . $_SESSION['picture'] . "' width='100'><br>";
echo "Email: " . $_SESSION['email'];
?>
