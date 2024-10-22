<?php 
session_start(); // Start the session before destroying it
$_SESSION = array(); // Unset all session variables
session_destroy(); // Destroy the session
session_write_close(); // Make sure the session is written and closed
setcookie(session_name(),'',0,'/'); // Clear the session cookie
require_once 'functions.php';  
redirect_to('index.php'); 
?>