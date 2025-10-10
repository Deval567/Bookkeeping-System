<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

require_once "../validations/login.validation.php";
require_once "../configs/dbc.php";
require_once "../models/users.class.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$roles;
$errors = [];

$validator = new LoginValidation();
$errors = $validator->validate($username, $password);

// ====== If input validation fails ======
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header("Location: ../index.php");
    exit;
} else {

    $user = new Users($conn, $username, $password, $roles);
    $user = $user->isUserExists();

    if (!$user) {
        $_SESSION['login_errors'] = ["No user found"];
        header("Location: ../index.php");
        exit;

    }else{
        if($user['password'] === $password){
            // Password is the same in the database
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../pages/dashboard.php");
            exit;
        }else{
            $_SESSION['login_errors'] = ["Incorrect username or password"];
            header("Location: ../index.php");
            exit;
        }
    }
}

