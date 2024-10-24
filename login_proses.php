<?php
//login_proses.php

session_start();
require_once("db.php");
require_once("functions.php");

$username = strtolower($_POST["username"]);
$password = $_POST["pass"];

// Modified SQL query to properly handle login cases
$sql = "SELECT * FROM user WHERE 
        email = ? OR 
        (first_name = ? AND last_name IS NULL) OR 
        (CONCAT(first_name, ' ', last_name) = ?)";

$result = $db->prepare($sql);
$result->execute([$username, $username, $username]); 

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