
<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}
$id = $_POST['id'];
$rule_id = $_POST['rule_id'];
$transaction_date = $_POST['transaction_date'];
$reference_no = $_POST['reference_no'];
$total_amount = $_POST['total_amount'];
$description = $_POST['description'];
$created_by = $_SESSION['user_id'];
$account_ids = $_POST['account_ids'];
$entry_types = $_POST['entry_types'];
$amounts = $_POST['amounts'] ?? [];
$action = $_POST['action'];


require_once '../validations/transactions.validation.php';
require_once '../configs/dbc.php';
require_once '../models/transactions.class.php';
require_once '../models/journalentries.class.php';
require_once '../models/chartofacc.class.php';

$validator = new TransactionsValidation();
$transaction = new Transaction($conn, $rule_id, $transaction_date, $reference_no, $description, $entry_types, $total_amount, $created_by);
$journal_entries = new JournalEntries($conn, null, null, null, null, null, null);
$chartofAcc = new ChartOfAccounts($conn, null, null, null, null);



switch ($action) {
    case 'add_transaction':
        $errors = $validator->validate($rule_id, $transaction_date, $reference_no, $total_amount, $description, $account_ids, $entry_types, $amounts);
        if (!empty($errors)) {
            $_SESSION['transaction_errors'] = $errors;
            header("Location: ../pages/transactions.php");
            exit;
        }
        $createdTransaction = $transaction->createTransaction($rule_id, $reference_no, $description, $transaction_date, $total_amount, $created_by);
        if ($createdTransaction) {
            $_SESSION['success_message'] = ["Transaction added successfully."];
            foreach ($account_ids as $index => $account_id) {
                $entry_type = $entry_types[$index];
                $amount = $amounts[$index];
                $accountName = $chartofAcc->getAccountNameById($account_id);
                $createdJournal = $journal_entries->createJournalEntry($createdTransaction, $account_id, $entry_type, $amount, $description, $transaction_date);

                if (!$createdJournal) {
                    $_SESSION['transaction_errors'] = ["Failed to add journal entry for account ", $accountName . "Please try again."];
                    header("Location: ../pages/transactions.php");
                    exit;
                } else {
                    $_SESSION['success_message'] = ["Transaction and journal entries added successfully."];
                    header("Location: ../pages/transactions.php");
                }
            }
        } else {
            $_SESSION['transaction_errors'] = ["Failed to add transaction. Please try again."];
            header("Location: ../pages/transactions.php");
            exit;
        }
        break;
    case 'delete_transaction':
        $deletedTransaction = $transaction->deleteTransaction($id);
        $deletedJournalEntries = $journal_entries->deleteJournalEntriesByTransactionId($id);
        if ($deletedTransaction) {
            $_SESSION['success_message'] = ["Transaction deleted successfully."];
        } else {
            $_SESSION['transaction_errors'] = ["Failed to delete transaction. Please try again."];
        }

        if ($deletedJournalEntries) {
            $_SESSION['success_message'] = ["Transaction and associated journal entries deleted successfully."];
        } else {
            $_SESSION['transaction_errors'] = ["Failed to delete associated journal entries. Please try again."];
        }
        header("Location: ../pages/transactions.php");
        exit;
        break;
    case 'update_transaction':
        $errors = $validator->validate($rule_id, $transaction_date, $reference_no, $total_amount, $description, $account_ids, $entry_types, $amounts);
        if (!empty($errors)) {
            $_SESSION['transaction_errors'] = $errors;
            header("Location: ../pages/transactions.php");
            exit;
        } else {
            $transaction_rule = $transaction->getransactionRuleIdById($id);
            $old_rule_id = $transaction_rule['rule_id'];

            $updatedTransaction = $transaction->updateTransaction($id, $rule_id, $reference_no, $description, $transaction_date, $total_amount);

            if (!$updatedTransaction) {
                $_SESSION['transaction_errors'] = ["Failed to update transaction. Please try again."];
                header("Location: ../pages/transactions.php");
                exit;
            }

            if ($rule_id == $old_rule_id) {

                $allUpdated = true;
                foreach ($account_ids as $index => $account_id) {
                    $entry_type = $entry_types[$index];
                    $amount = $amounts[$index];

                    $updatedJournal = $journal_entries->updateJournalEntry($id, $account_id, $entry_type, $amount, $description, $transaction_date);

                    if (!$updatedJournal) {
                        $accountName = $chartofAcc->getAccountNameById($account_id);
                        $_SESSION['transaction_errors'] = ["Failed to update journal entry for account " . $accountName['account_name'] . ". Please try again."];
                        $allUpdated = false;
                        break;
                    }
                }

                if ($allUpdated) {
                    $_SESSION['success_message'] = ["Transaction and journal entries updated successfully."];
                }

                header("Location: ../pages/transactions.php");
                exit;
            } else {

                $deletedJournalEntries = $journal_entries->deleteJournalEntriesByTransactionId($id);

                if (!$deletedJournalEntries) {
                    $_SESSION['transaction_errors'] = ["Failed to delete existing journal entries. Please try again."];
                    header("Location: ../pages/transactions.php");
                    exit;
                }

                $allCreated = true;
                foreach ($account_ids as $index => $account_id) {
                    $entry_type = $entry_types[$index];
                    $amount = $amounts[$index];

                    $createdJournal = $journal_entries->createJournalEntry($id, $account_id, $entry_type, $amount, $description, $transaction_date);

                    if (!$createdJournal) {
                        $accountName = $chartofAcc->getAccountNameById($account_id);
                        $_SESSION['transaction_errors'] = ["Failed to create journal entry for account " . $accountName['account_name'] . ". Please try again."];
                        $allCreated = false;
                        break;
                    }
                }

                if ($allCreated) {
                    $_SESSION['success_message'] = ["Transaction and journal entries updated successfully."];
                }

                header("Location: ../pages/transactions.php");
                exit;
            }
            break;
        }
}
