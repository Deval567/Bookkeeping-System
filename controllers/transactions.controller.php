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

$id = $_POST['id'] ?? null;
$rule_id = $_POST['rule_id'] ?? null;
$transaction_date = $_POST['transaction_date'] ?? null;
$reference_no = $_POST['reference_no'] ?? null;
$total_amount = $_POST['total_amount'] ?? null;
$description = $_POST['description'] ?? null;
$created_by = $_SESSION['user_id'];
$rule_line_ids = $_POST['rule_line_ids'] ?? [];
$amounts = $_POST['amounts'] ?? [];
$action = $_POST['action'] ?? null;

require_once '../validations/transactions.validation.php';
require_once '../configs/dbc.php';
require_once '../models/transactions.class.php';
require_once '../models/journalentries.class.php';
require_once '../models/transactionrulelines.class.php';

$validator = new TransactionsValidation();
$transaction = new Transaction($conn, $rule_id, $transaction_date, $reference_no, $description, null, $total_amount, $created_by);
$journal_entries = new JournalEntries($conn, null, null, null, null, null);
$transactionRuleLines = new TransactionRuleLines($conn, null, null, null, null);

switch ($action) {
    case 'add_transaction':
        // Validate using rule_line_ids
        $errors = $validator->validate($rule_id, $transaction_date, $reference_no, $total_amount, $description, $rule_line_ids, $amounts);
        if (!empty($errors)) {
            $_SESSION['transaction_errors'] = $errors;
            header("Location: ../pages/transactions.php");
            exit;
        }

        if ($transaction->isTransactionExists($reference_no)) {
            $_SESSION['transaction_errors'] = ["A transaction with reference number '$reference_no' already exists."];
            header("Location: ../pages/transactions.php");
            exit;
        }

        $createdTransaction = $transaction->createTransaction($rule_id, $reference_no, $description, $transaction_date, $total_amount, $created_by);

        if ($createdTransaction) {
            foreach ($rule_line_ids as $index => $rule_line_id) {
                $amount = $amounts[$index];

                // Get entry type from transaction_rule_lines
                $rule_line = $transactionRuleLines->getRuleLineById($rule_line_id);

                if (!$rule_line) {
                    $_SESSION['transaction_errors'] = ["Invalid rule line ID: " . $rule_line_id];
                    header("Location: ../pages/transactions.php");
                    exit;
                }

                $entry_type = $rule_line['entry_type'];

                $createdJournal = $journal_entries->createJournalEntry(
                    $createdTransaction,
                    $rule_line_id,
                    $entry_type,
                    $amount,
                    $description,
                    $transaction_date
                );

                if (!$createdJournal) {
                    $_SESSION['transaction_errors'] = ["Failed to add journal entry for rule line ID " . $rule_line_id . ". Please try again."];
                    header("Location: ../pages/transactions.php");
                    exit;
                }
            }

            $_SESSION['success_message'] = ["Transaction and journal entries added successfully."];
            header("Location: ../pages/transactions.php");
            exit;
        } else {
            $_SESSION['transaction_errors'] = ["Failed to add transaction. Please try again."];
            header("Location: ../pages/transactions.php");
            exit;
        }
        break;

    case 'delete_transaction':
        $deletedJournalEntries = $journal_entries->deleteJournalEntriesByTransactionId($id);
        $deletedTransaction = $transaction->deleteTransaction($id);

        if ($deletedTransaction && $deletedJournalEntries) {
            $_SESSION['success_message'] = ["Transaction and associated journal entries deleted successfully."];
        } else {
            $_SESSION['transaction_errors'] = ["Failed to delete transaction or associated journal entries. Please try again."];
        }

        header("Location: ../pages/transactions.php");
        exit;
        break;

    case 'update_transaction':
        // Validate using rule_line_ids
        $errors = $validator->validate($rule_id, $transaction_date, $reference_no, $total_amount, $description, $rule_line_ids, $amounts);
        if (!empty($errors)) {
            $_SESSION['transaction_errors'] = $errors;
            header("Location: ../pages/transactions.php");
            exit;
        }

        if ($transaction->isTransactionExists($reference_no, $id)) {
            $_SESSION['transaction_errors'] = ["A transaction with reference number '$reference_no' already exists."];
            header("Location: ../pages/transactions.php");
            exit;
        }

        $updatedTransaction = $transaction->updateTransaction($id, $rule_id, $reference_no, $description, $transaction_date, $total_amount);

        if (!$updatedTransaction) {
            $_SESSION['transaction_errors'] = ["Failed to update transaction. Please try again."];
            header("Location: ../pages/transactions.php");
            exit;
        }

        // Always delete old journal entries and create new ones for updates
        $deletedJournalEntries = $journal_entries->deleteJournalEntriesByTransactionId($id);

        if (!$deletedJournalEntries) {
            $_SESSION['transaction_errors'] = ["Failed to delete existing journal entries. Please try again."];
            header("Location: ../pages/transactions.php");
            exit;
        }

        // Create new journal entries
        $allSuccess = true;
        foreach ($rule_line_ids as $index => $rule_line_id) {
            $amount = $amounts[$index];

            // Get entry type from transaction_rule_lines
            $rule_line = $transactionRuleLines->getRuleLineById($rule_line_id);

            if (!$rule_line) {
                $_SESSION['transaction_errors'] = ["Invalid rule line ID: " . $rule_line_id];
                header("Location: ../pages/transactions.php");
                exit;
            }

            $entry_type = $rule_line['entry_type'];

            $createdJournal = $journal_entries->createJournalEntry(
                $id,
                $rule_line_id,
                $entry_type,
                $amount,
                $description,
                $transaction_date
            );

            if (!$createdJournal) {
                $_SESSION['transaction_errors'] = ["Failed to create journal entry for rule line ID " . $rule_line_id . ". Please try again."];
                $allSuccess = false;
                break;
            }
        }

        if ($allSuccess) {
            $_SESSION['success_message'] = ["Transaction and journal entries updated successfully."];
        }

        header("Location: ../pages/transactions.php");
        exit;
        break;
}
