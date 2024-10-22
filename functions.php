<?php

function redirect_to($url){
    header("location:$url");
}

function is_admin() {
    return isset($_SESSION['is_admin']);
}