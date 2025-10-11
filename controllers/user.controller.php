<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/users.php");
    exit;
}

require_once "../validations/user.validation.php";
require_once "../configs/dbc.php";
require_once "../models/users.class.php";

$id = $_POST['id'];
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$roles = trim($_POST['role']);
$action = $_POST['action'];
$errors = [];


$user = new Users($conn, $id, $username, $password, $roles);
$validator = new UserValidation();



switch ($action) {

    case "add_user":
        $errors = $validator->validate($username, $password, $roles);
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            header("Location: ../pages/users.php");
            exit;
        } else {
            $existingUser = $user->isUserExists();

            if (!$existingUser) {

                $created = $user->createUser($username, $password, $roles);

                if ($created) {
                    $_SESSION['success_message'] = "User created successfully.";
                    header("Location: ../pages/users.php");
                    exit;
                } else {
                    $_SESSION['user_errors'] = ["Failed to create user. Please try again."];
                    header("Location: ../pages/users.php");
                    exit;
                }
            } else {
                $_SESSION['user_errors'] = ["User already exists."];
                header("Location: ../pages/users.php");
                exit;
            }
        }
        break;

    case "delete_user":
        $deleted = $user->deleteUser($id);
        if ($deleted) {
            $_SESSION['success_message'] = "User deleted successfully.";
            header("Location: ../pages/users.php");
            exit;
        } else {
            $_SESSION['user_errors'] = ["Failed to delete user. Please try again."];
            header("Location: ../pages/users.php");
            exit;
        }
        break;

    case "update_user":
         $errors = $validator->validate($username, $password, $roles);
        if (!empty($errors)) {
            $_SESSION['user_errors'] = $errors;
            header("Location: ../pages/users.php");
            exit;
        }else
        {
        $existingUser = $user->isUserExists();

        if ($existingUser && $existingUser['id'] != $id) {
            $_SESSION['user_errors'] = ["Username already taken by another user."];
            header("Location: ../pages/users.php");
            exit;
        }

        $updated = $user->updateUser($id, $conn, $username, $password, $roles);
        $errors = $validator->validate($username, $password, $roles);
        if ($updated) {
            $_SESSION['success_message'] = "User updated successfully.";
            header("Location: ../pages/users.php");
            exit;
        } else {
            $_SESSION['user_errors'] = ["Failed to update user. Please try again."];
            header("Location: ../pages/users.php");
            exit;
        }
        break;
        }
    }
        

