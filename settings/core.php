<?php
//start session
session_start(); 

//for header redirection
ob_start();

//function to check for login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

//function to check for role (admin, customer, etc)
function isAdmin() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '1');
}

?>