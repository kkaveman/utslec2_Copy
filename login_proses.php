<?php
//login_proses.php

session_start();
require_once("db.php");
require_once("functions.php");

$username = strtolower($_POST["username"]);
$password = $_POST["pass"];

$sql = "SELECT * from user WHERE (email= ? or concat(first_name,' ',last_name)=?)";
$result = $db->prepare($sql);
$result->execute([$username, $username]); // Pass the username twice, once for email and once for name concatenation

$row = $result->fetch(PDO::FETCH_ASSOC);

if(!$row){
    echo "User Not Found";
}
else{
    if(password_verify($password, $row["pass"])){
        $_SESSION['username'] = $row["first_name"];
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["is_admin"] = $row["is_admin"];
        redirect_to("dashboard.php");
    }
    else{
        echo "Wrong password";
    }
}
