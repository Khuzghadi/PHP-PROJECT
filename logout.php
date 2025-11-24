<?php
session_start();
session_unset();    // remove all session variables
session_destroy();  // destroy the active session
header("Location: loginForm.html");
exit;
?>
