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

$id = $_POST['id'];
$rule_id = $_POST['rule_id'];
$accounts = $_POST['account_id'];
$entries = $_POST['entry_type'];
$action = $_POST['action'];
require_once '../models/transactionrulelines.class.php';
require_once '../configs/dbc.php';
require_once '../validations/transactionrulelines.validation.php';
require_once '../models/transactionrules.class.php';
require_once '../models/chartofacc.class.php';

$validation = new transactionRuleLinesValidation($rule_id, $accounts, $entries);
$chartofAcc = new ChartofAccounts($conn, null, null, null, null,null);
$transactionRules = new transactionRules($conn, null, null, null, null);
$transactionRuleLines = new TransactionRuleLines($conn, null, null, null, null);
$errors = $validation->validate($rule_id, $accounts, $entries);


switch ($action) {
    case 'add_rule_lines':
        if (!empty($errors)) {
            $_SESSION['transactionrulelines_errors'] = $errors;
            header("Location: ../pages/transactionrulelines.php");
            exit;
        }

        $errorMessages = [];
        $successMessages = [];

        foreach ($accounts as $index => $account_id) {
            $entry_type = $entries[$index];
            $accountName = $chartofAcc->getAccountNameById($account_id);
            $ruleName = $transactionRules->getRuleNameById($rule_id);

            if ($transactionRuleLines->isRuleLineExists($rule_id, $account_id, $entry_type)) {
                $errorMessages[] = "Rule name <b>$ruleName</b> with Account name <b>$accountName</b> and entry type <b>" . ucfirst($entry_type) . "</b> already exists.";
                continue;
            }

            if ($transactionRuleLines->createTransactionRuleLine($rule_id, $account_id, $entry_type)) {
                $successMessages[] = "Transaction rule line <b>$ruleName</b> with account <b>$accountName</b> and entry type <b>" . ucfirst($entry_type) . "</b> created successfully.";
            } else {
                $errorMessages[] = "Failed to create rule line for Account <b>$accountName</b>.";
            }
        }

        if (!empty($errorMessages)) {
            $_SESSION['transactionrulelines_errors'] = $errorMessages;
        }
        if (!empty($successMessages)) {
            $_SESSION['success_message'] = implode('<br>', $successMessages);
        }

        header("Location: ../pages/transactionrulelines.php");
        exit;
        break;
    case 'delete_rule_line':
        $id = $_POST['id'];
        if ($transactionRuleLines->deleteTransactionRuleLine($id)) {
            $_SESSION['success_message'] = "Transaction Rule Line deleted successfully.";
        } else {
            $_SESSION['transactionrulelines_errors'] = ["Failed to delete transaction rule line. Please try again."];
        }
        header("Location: ../pages/transactionrulelines.php");
        exit;
        break;
    case 'update_rule_line':
        $errorMessages = [];
        $successMessages = [];

        if (!empty($errors)) {
            $_SESSION['transactionrulelines_errors'] = $errors;
            header("Location: ../pages/transactionrulelines.php");
            exit;
        }

        foreach ($accounts as $index => $account_id) {
            $entry_type = $entries[$index];
            $accountName = $chartofAcc->getAccountNameById($account_id);
            $ruleName = $transactionRules->getRuleNameById($rule_id);

            if ($transactionRuleLines->isRuleLineExists($rule_id, $account_id, $entry_type, $id)) {
                $errorMessages[] = "Rule name <b>$ruleName</b> with Account name <b>$accountName</b> and entry type <b>" . ucfirst($entry_type) . "</b> already exists.";
                continue; 
            }

            $updated = $transactionRuleLines->updateTransactionRuleLine($id, $rule_id, $account_id, $entry_type);
            if ($updated) {
                $successMessages[] = "Transaction rule line <b>$ruleName</b> with account <b>$accountName</b> and entry type <b>" . ucfirst($entry_type) . "</b> updated successfully.";
            } else {
                $errorMessages[] = "Failed to update rule line for account <b>$accountName</b>.";
            }
        }
        if (!empty($errorMessages)) {
            $_SESSION['transactionrulelines_errors'] = $errorMessages;
        }
        if (!empty($successMessages)) {
            $_SESSION['success_message'] = implode("<br>", $successMessages);
        }

        header("Location: ../pages/transactionrulelines.php");
        exit;
        break;
}
