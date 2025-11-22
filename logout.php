<?php
session_start();
// Unset all session values
$_SESSION = array();
// Destroy the session cookie and data
session_destroy();
// Redirect to login
header("Location: login.php");
exit;
?>