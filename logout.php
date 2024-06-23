<?php
session_start();
session_unset();
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php");
exit();
?>
