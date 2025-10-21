<?php

class TransactionsValidation
{

    public function validate($rule_id, $date, $reference_no, $total_amount, $description, $account_ids, $entry_types, $amounts)
    {
        $errors = [];

        if (empty($rule_id)) {
            $errors['rule_id_empty'] = "Please select a rule";
        }

        if (empty($date)) {
            $errors['date_empty'] = "Please fill the Transaction Date field";
        }

        if (empty($reference_no)) {
            $errors['reference_no_empty'] = "Please fill the Reference Number field";
        }

        if (empty($total_amount)) {
            $errors['total_amount_empty'] = "Please fill the Total Amount field";
        } elseif (!is_numeric($total_amount) || $total_amount <= 0) {
            $errors['total_amount_invalid'] = "Total Amount must be a positive number";
        }

        if (empty($description)) {
            $errors['description_empty'] = "Please fill the Description field";
        }

        if (empty($account_ids) || empty($entry_types) || empty($amounts)) {
            $errors['entries_empty'] = "Please provide at least one transaction entry";
        } else {
            $total_debit = 0;
            $total_credit = 0;

            foreach ($amounts as $index => $amount) {
                if (!is_numeric($amount) || $amount <= 0) {
                    $errors['amount_invalid_' . $index] = "Amount for entry " . ($index + 1) . " must be a positive number";
                }

                // Sum debit and credit amounts
                if ($entry_types[$index] === 'debit') {
                    $total_debit += $amount;
                } elseif ($entry_types[$index] === 'credit') {
                    $total_credit += $amount;
                }
            }

            // ✅ Check if Total Amount = Total Debit OR Total Amount = Total Credit
            if ($total_amount != $total_debit && $total_amount != $total_credit) {
                $errors['total_mismatch'] = "Total Amount must be equal to either Total Debit (" . $total_debit . ") or Total Credit (" . $total_credit . ")";
            }
        }

        return $errors;
    }
}
