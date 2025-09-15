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

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$errors = [];

// ====== Input Validations ======
if (is_username_empty($username)) {
    $errors['username_empty'] = "Please fill the Username field";
}
if (is_password_empty($password)) {
    $errors['password_empty'] = "Please fill the Password field";
}
if (is_username_invalid($username)) {
    $errors['invalid_username'] = "Please enter a valid username";
}
if (is_username_length_invalid($username)) {
    $errors['invalid_username'] = "Username must be at least 6 characters long";
}
if (is_password_length_invalid($password)) {
    $errors['invalid_password'] = "Password must be at least 6 characters long";
}

// ====== If input validation fails ======
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header("Location: ../index.php");
    exit;
}

// ====== Check database ======
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    $_SESSION['login_errors'] = ["Something went wrong. Please try again later."];
    header("Location: ../index.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ====== User found? ======
if ($row = mysqli_fetch_assoc($result)) {
    // Verify password (hashed in DB)
    if ($row['password'] == $password) {
        // Login successful
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        // Wrong password
        $_SESSION['login_errors'] = ["Incorrect username or password"];
        header("Location: ../index.php");
        exit;
    }
} else {
    // No user found
    $_SESSION['login_errors'] = ["No user Found"];
    header("Location: ../index.php");
    exit;
}
