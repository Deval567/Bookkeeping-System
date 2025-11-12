<?php

class TransactionRuleLinesValidation
{
    public function validate($rule_id, $account_ids, $entry_types)
    {
        $errors = [];

        $account_ids = isset($account_ids) ? (array) $account_ids : [];
        $entry_types = isset($entry_types) ? (array) $entry_types : [];

        $account_ids = array_filter($account_ids, fn($a) => $a !== '');
        $entry_types = array_filter($entry_types, fn($e) => $e !== '');

        if (empty($rule_id)) {
            $errors['rule_id_empty'] = "Please select a Transaction Rule";
        }

        if (count($account_ids) === 0) {
            $errors['account_ids_empty'] = "Please add at least one account line";
        } else {
            foreach ($account_ids as $index => $account_id) {
                if (empty($account_id)) {
                    $errors["account_id_{$index}_empty"] = "Please select an account for line " . ($index + 1);
                }
            }
        }

        if (count($entry_types) === 0) {
            $errors['entry_types_empty'] = "Please add at least one entry type";
        } else {
            foreach ($entry_types as $index => $entry_type) {
                if (empty($entry_type) || !in_array(strtolower($entry_type), ['debit', 'credit'])) {
                    $errors["entry_type_{$index}_invalid"] = "Please select a valid entry type (Debit or Credit) for line " . ($index + 1);
                }
            }
        }

        $has_debit = false;
        $has_credit = false;
        
        foreach ($entry_types as $entry_type) {
            if (strtolower($entry_type) === 'debit') {
                $has_debit = true;
            }
            if (strtolower($entry_type) === 'credit') {
                $has_credit = true;
            }
        }
        
        if (!$has_debit || !$has_credit) {
            $errors['entry_types_incomplete'] = "Transaction must have at least one debit and one credit entry";
        }

        $account_ids_cleaned = array_filter($account_ids, fn($a) => !empty($a));
        $unique_accounts = array_unique($account_ids_cleaned);
        
        if (count($account_ids_cleaned) !== count($unique_accounts)) {
            $errors['duplicate_accounts'] = "Duplicate accounts are not allowed. Each account can only appear once in the transaction rule";
        }

        return $errors;
    }
}