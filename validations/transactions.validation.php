<?php

class TransactionsValidation {

    public function validate($rule_id, $date, $reference_no, $total_amount, $description, $account_ids, $entry_types, $amounts) {
        $errors = [];

        // Rule ID validation
        if (empty($rule_id)) {
            $errors['rule_id_empty'] = "Please select a rule.";
        }

        // Date validation
        if (empty($date)) {
            $errors['date_empty'] = "Please fill the Transaction Date field.";
        }

        // Reference Number validation
        if (empty($reference_no)) {
            $errors['reference_no_empty'] = "Please fill the Reference Number field.";
        }

        // Total Amount validation
        if (empty($total_amount)) {
            $errors['total_amount_empty'] = "Please fill the Total Amount field.";
        } elseif (!is_numeric($total_amount) || $total_amount <= 0) {
            $errors['total_amount_invalid'] = "Total Amount must be a positive number.";
        }

        // Description validation
        if (empty($description)) {
            $errors['description_empty'] = "Please fill the Description field.";
        }

        // Entries validation (accounts, debit/credit, amounts)
        if (empty($account_ids) || empty($entry_types) || empty($amounts)) {
            $errors['entries_empty'] = "Please provide at least one transaction entry.";
        } else {
            $total_debit = 0;
            $total_credit = 0;

            foreach ($amounts as $index => $amount) {
                // Check if valid amount
                if (!is_numeric($amount) || $amount <= 0) {
                    $errors["amount_invalid_$index"] = "Amount for entry " . ($index + 1) . " must be a positive number.";
                }

                // Sum debit or credit
                if (isset($entry_types[$index]) && $entry_types[$index] === 'debit') {
                    $total_debit += floatval($amount);
                } elseif (isset($entry_types[$index]) && $entry_types[$index] === 'credit') {
                    $total_credit += floatval($amount);
                }
            }

            // Check if debit and credit are balanced
            if ($total_debit !== $total_credit) {
                $errors['balance_error'] = "Total Debit ($total_debit) must equal Total Credit ($total_credit).";
            }

            // Check if total amount matches the debit/credit total
            if (floatval($total_amount) !== $total_debit) {
                $errors['total_mismatch'] = "Total Amount ($total_amount) must equal total debit/credit ($total_debit).";
            }
        }

        return $errors;
    }
}

?>
