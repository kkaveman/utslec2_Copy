<?php

require_once("db.php");
require_once("functions.php");
$fname = strtolower($_POST["fname"]);
$lname = strtolower($_POST["lname"]);
$email = strtolower($_POST["email"]);
$pass = $_POST["pass"];

$en_pass = password_hash($pass, PASSWORD_BCRYPT);
$sql = "INSERT INTO user(first_name, last_name, email,pass)
VALUES(?,?,?,?)";
$result = $db->prepare($sql);
$result->execute([$fname,$lname,$email,$en_pass]);
redirect_to("login.php");


