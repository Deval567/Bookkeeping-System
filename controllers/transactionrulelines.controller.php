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

$action = $_POST['action'];
require_once '../models/transactionrulelines.class.php';
require_once '../configs/dbc.php';
require_once '../validations/transactionrulelines.validation.php';
require_once '../models/transactionrules.class.php';
require_once '../models/chartofacc.class.php';

$chartofAcc = new ChartofAccounts($conn, null, null, null, null, null);
$transactionRules = new transactionRules($conn, null, null, null, null);
$transactionRuleLines = new TransactionRuleLines($conn, null, null, null, null);

switch ($action) {
    case 'add_rule_lines':
        $rule_id = $_POST['rule_id'];
        $accounts = $_POST['account_id'];
        $entries = $_POST['entry_type'];

        $validation = new transactionRuleLinesValidation($rule_id, $accounts, $entries);
        $errors = $validation->validate($rule_id, $accounts, $entries);

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

    case 'update_rule_lines':
        $original_rule_id = $_POST['original_rule_id'];
        $rule_id = $_POST['rule_id'];
        $accounts = $_POST['account_id'];
        $entries = $_POST['entry_type'];
        $line_ids = $_POST['line_ids'] ?? [];

        $validation = new transactionRuleLinesValidation($rule_id, $accounts, $entries);
        $errors = $validation->validate($rule_id, $accounts, $entries);

        if (!empty($errors)) {
            $_SESSION['transactionrulelines_errors'] = $errors;
            header("Location: ../pages/transactionrulelines.php");
            exit;
        }

        $errorMessages = [];
        $successMessages = [];

        $existing_lines = $transactionRuleLines->getRuleLinesByRuleId($original_rule_id);
        $existing_ids = array_column($existing_lines, 'id');

        $submitted_ids = array_filter($line_ids, function ($id) {
            return !empty($id);
        });

        $ids_to_delete = array_diff($existing_ids, $submitted_ids);

        foreach ($ids_to_delete as $delete_id) {
            $result = $transactionRuleLines->deleteTransactionRuleLine($conn, $delete_id);
            if ($result['success']) {
                $successMessages[] = "Removed 1 rule line.";
            }
        }

        foreach ($accounts as $index => $account_id) {
            $entry_type = $entries[$index];
            $line_id = !empty($line_ids[$index]) ? $line_ids[$index] : null;

            $accountName = $chartofAcc->getAccountNameById($account_id);
            $ruleName = $transactionRules->getRuleNameById($rule_id);

            if ($line_id) {
                $updated = $transactionRuleLines->updateTransactionRuleLine($line_id, $rule_id, $account_id, $entry_type);
                if ($updated) {
                    $successMessages[] = "Updated <b>$ruleName</b> → <b>$accountName</b> (<b>" . ucfirst($entry_type) . "</b>)";
                } else {
                    $errorMessages[] = "Failed to update rule line for account <b>$accountName</b>.";
                }
            } else {
                if ($transactionRuleLines->isRuleLineExists($rule_id, $account_id, $entry_type)) {
                    $errorMessages[] = "Rule <b>$ruleName</b> → <b>$accountName</b> (<b>" . ucfirst($entry_type) . "</b>) already exists.";
                    continue;
                }

                if ($transactionRuleLines->createTransactionRuleLine($rule_id, $account_id, $entry_type)) {
                    $successMessages[] = "Created <b>$ruleName</b> → <b>$accountName</b> (<b>" . ucfirst($entry_type) . "</b>)";
                } else {
                    $errorMessages[] = "Failed to create rule line for <b>$accountName</b>.";
                }
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

    case 'delete_rule_lines':
        $ids = $_POST['ids'] ?? [];
        $rule_id = $_POST['rule_id'] ?? null;
        $page = $_POST['page'] ?? 'transactionrulelines';

        if (empty($ids)) {
            $_SESSION['transactionrulelines_errors'] = ["No rule lines selected for deletion."];
            header("Location: ../pages/transactionrulelines.php?page=$page");
            exit;
        }

        // Get the rule name before deleting
        $ruleName = $transactionRules->getRuleNameById($rule_id) ?? 'Unknown Rule';

        $successCount = 0;
        $errorMessages = [];

        foreach ($ids as $id) {
            $result = $transactionRuleLines->deleteTransactionRuleLine($conn, $id);
            if ($result['success']) {
                $successCount++;
            } else {
                $errorMessages[] = "Failed to delete a rule line for <b>$ruleName</b> — it may be in use.";
            }
        }

        if ($successCount > 0) {
            $_SESSION['success_message'] = "Successfully deleted <b>$successCount</b> rule line(s)</b> under <b>$ruleName</b>.";
        }

        if (!empty($errorMessages)) {
            $_SESSION['transactionrulelines_errors'] = $errorMessages;
        }

        header("Location: ../pages/transactionrulelines.php?page=$page");
        exit;
        break;
}
