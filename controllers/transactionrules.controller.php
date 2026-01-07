<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/dashboard.php");
    exit;
}

if (!isset($_SESSION['username'], $_SESSION['user_id'], $_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You don't have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['role'] !== 'Admin') {
    $_SESSION['dashboard_errors'] = ["Access denied. You don't have access to this page."];
    header("Location: ../pages/dashboard.php");
    exit();
}

require_once "../validations/transactionrules.validation.php";
require_once "../configs/dbc.php";
require_once "../models/transactionrules.class.php";

$id = $_POST['id'] ?? null;
$rule_name = $_POST['rule_name'] ?? '';
$category = $_POST['category'] ?? '';
$description = $_POST['description'] ?? '';
$action = $_POST['action'] ?? '';

$validator = new transactionRulesValidation();
$transaction = new transactionRules($conn, $id, $rule_name, $category, $description);
$transactionRules = new transactionRules($conn, null, null, null, null); // For delete/getRuleName
$errors = $validator->validate($rule_name, $category, $description);

switch ($action) {
    case "add_rule":
        if (!empty($errors)) {
            $_SESSION['transactionrules_errors'] = $errors;
        } else {
            $created = $transaction->createTransactionRule($rule_name, $category, $description);
            if ($created) {
                $_SESSION['success_message'] = "Transaction rule created successfully.";
            } else {
                $_SESSION['transactionrules_errors'] = ["Failed to create transaction rule. Please try again."];
            }
        }
        header("Location: ../pages/transactionrules.php");
        exit;
        break;

    case "update_rule":
        if (!empty($errors)) {
            $_SESSION['transactionrules_errors'] = $errors;
        } else {
            $updated = $transaction->updateTransactionRule($id, $rule_name, $category, $description);
            if ($updated) {
                $_SESSION['success_message'] = "Transaction rule updated successfully.";
            } else {
                $_SESSION['transactionrules_errors'] = ["Failed to update transaction rule. Please try again."];
            }
        }
        header("Location: ../pages/transactionrules.php");
        exit;
        break;

    case "delete_rule":
        $rule_id = $_POST['id'] ?? null;
        $page = $_POST['page'] ?? 'transactionrules';

        if (empty($rule_id)) {
            $_SESSION['transactionrules_errors'] = ["No transaction rule selected for deletion."];
            header("Location: ../pages/transactionrules.php?page=$page");
            exit;
        }

        $ruleName = $transactionRules->getRuleNameById($rule_id);
        $deleted = $transactionRules->deleteTransactionRule($rule_id);

        if ($deleted['success']) {
            $_SESSION['success_message'] = "Transaction Rule <b>$ruleName</b> deleted successfully.";
        } else {
            $error = $deleted['error'] ?? 'unknown_error';
            if ($error === 'rule_in_use') {
                $_SESSION['transactionrules_errors'] = ["Cannot delete <b>$ruleName</b>  it is currently in use."];
            } else {
                $_SESSION['transactionrules_errors'] = ["Failed to delete <b>$ruleName</b>. Please try again."];
            }
        }

        header("Location: ../pages/transactionrules.php?page=$page");
        exit;
        break;
}
