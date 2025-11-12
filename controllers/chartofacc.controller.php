<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/dashboard.php");
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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/users.php");
    exit;
}

require_once "../validations/chartofacc.validation.php";
require_once "../configs/dbc.php";
require_once "../models/chartofacc.class.php";

$id = $_POST['id'] ?? null;
$account_name = htmlspecialchars(trim($_POST['acc_name']));
$account_type = htmlspecialchars(trim($_POST['acc_type']));
$cash_flow_category = $_POST['cash_flow_category'];
$description = htmlspecialchars(trim($_POST['description']));
$action = htmlspecialchars(trim($_POST['action']));
$errors = [];

$chartofacc = new ChartofAccounts($conn, $id, $account_name, $account_type, $cash_flow_category, $description);
$validator = new ChartofAccountValidation();

switch ($action) {
    case "add_account":
        $errors = $validator->validate($account_name, $account_type, $cash_flow_category, $description);
        if (!empty($errors)) {
            $_SESSION['chartofacc_errors'] = $errors;
            header("Location: ../pages/chartofaccounts.php");
            exit;
        } else {
            $existingAccount = $chartofacc->isAccountExists();

            if (!$existingAccount) {

                $created = $chartofacc->createAccount($account_name, $account_type, $cash_flow_category, $description);

                if ($created) {
                    $_SESSION['success_message'] = "Account created successfully.";
                    header("Location: ../pages/chartofaccounts.php");
                    exit;
                } else {
                    $_SESSION['chartofacc_errors'] = ["Failed to create account. Please try again."];
                    header("Location: ../pages/chartofaccounts.php");
                    exit;
                }
            } else {
                $_SESSION['chartofacc_errors'] = ["Account already exists."];
                header("Location: ../pages/chartofaccounts.php");
                exit;
            }
        }
        break;
    case "update_account":
        $errors = $validator->validate($account_name, $account_type, $cash_flow_category, $description);
        if (!empty($errors)) {
            $_SESSION['chartofacc_errors'] = $errors;
            header("Location: ../pages/chartofaccounts.php");
            exit;
        } else {
            $updated = $chartofacc->updateAccount($id, $account_name, $account_type, $cash_flow_category, $description);

            if ($updated) {
                $_SESSION['success_message'] = "Account updated successfully.";
                header("Location: ../pages/chartofaccounts.php");
                exit;
            } else {
                $_SESSION['chartofacc_errors'] = ["Failed to update account. Please try again."];
                header("Location: ../pages/chartofaccounts.php");
                exit;
            }
        }
        break;

    case "delete_account":
        $result = $chartofacc->deleteAccount($conn, $id);

        if ($result['success']) {
            $_SESSION['success_message'] = "Account deleted successfully.";
            header("Location: ../pages/chartofaccounts.php");
            exit;
        } else {
            if ($result['error'] === 'account_in_use') {
                $_SESSION['chartofacc_errors'] = ["Cannot delete account - it is currently used in transaction rules."];
            } else {
                $_SESSION['chartofacc_errors'] = ["Failed to delete account. Please try again."];
            }
            header("Location: ../pages/chartofaccounts.php");
            exit;
        }
        break;
}
