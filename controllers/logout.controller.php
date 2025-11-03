<?php
session_start();
session_unset();
$_SESSION['logout_message'] = ["You have been signed out successfully."];
header("Location: ../index.php");
exit();
?>
