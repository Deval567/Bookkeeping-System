<?php

session_start();

$_SESSION = array();


session_destroy();

$_SESSION['logout_message'] = "You have been signed out successfully.";
header("Location: ../index.php");
exit();
?>