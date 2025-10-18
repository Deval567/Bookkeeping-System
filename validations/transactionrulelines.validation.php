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

        return $errors;
    }
}
