<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/users.php");
    exit;
}
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] !== 'Admin') {
    $_SESSION['dashboard_errors'] = ["Access denied. You dont have access to this page."];
    header("Location: ../pages/dashboard.php"); 
    exit();
}
require_once "../validations/transactionrules.validation.php";
require_once "../configs/dbc.php";
require_once "../models/transactionrules.class.php";


$id = $_POST['id']; 
$rule_name = $_POST['rule_name'];
$category = $_POST['category'];
$description = $_POST['description'];
$action = $_POST['action'];
$errors = [];

$validator = new transactionRulesValidation();
$transaction= new transactionRules($conn, $id, $rule_name, $category,  $description);
$errors = $validator->validate($rule_name, $category,  $description);



switch ($action) {
    case "add_rule":
        if (!empty($errors)) {
            $_SESSION['transactionrules_errors'] = $errors;
            header("Location: ../pages/transactionrules.php");
            exit;
        } else {
            $created = $transaction->createTransactionRule($rule_name, $category, $description);
            if ($created) {
                $_SESSION['success_message'] = "Transaction rule created successfully.";
                header("Location: ../pages/transactionrules.php");
                exit;
            } else {
                $_SESSION['transactionrules_errors'] = ["Failed to create transaction rule. Please try again."];
                header("Location: ../pages/transactionrules.php");
                exit;
            }
        }
        break;
        case "delete_rule":
            $deleted = $transaction->deleteTransactionRule($id);
            if ($deleted) {
                $_SESSION['success_message'] = "Transaction Rule deleted successfully.";
            } else {
                $_SESSION['transactionrules_errors'] = ["Failed to delete transaction rule. Please try again."];
            }
            header("Location: ../pages/transactionrules.php");
            exit;
            break;
        case "update_rule":
            if (!empty($errors)) {
                $_SESSION['transactionrules_errors'] = $errors;
                header("Location: ../pages/transactionrules.php");
                exit;
            } else {
                $updated = $transaction->updateTransactionRule($id, $rule_name, $category, $description);
                if ($updated) {
                    $_SESSION['success_message'] = "Transaction rule updated successfully.";
                    header("Location: ../pages/transactionrules.php");
                    exit;
                } else {
                    $_SESSION['transactionrules_errors'] = ["Failed to update transaction rule. Please try again."];
                    header("Location: ../pages/transactionrules.php");
                    exit;
                }
            }
            break;

}

