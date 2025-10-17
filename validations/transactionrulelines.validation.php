<?php

class TransactionRuleLinesValidation
{
    public function validate($rule_id, $account_ids, $entry_types)
    {
        $errors = [];

        if (empty($rule_id)) {
            $errors['rule_id_empty'] = "Please select a Transaction Rule";
        }

        if (empty($account_ids) || !is_array($account_ids) || count($account_ids) === 0) {
            $errors['account_ids_empty'] = "Please add at least one account line";
        } else {
            foreach ($account_ids as $index => $account_id) {
                if (empty($account_id)) {
                    $errors["account_id_{$index}_empty"] = "Please select an account for line " . ($index + 1);
                }
            }
        }

        if (empty($entry_types) || !is_array($entry_types) || count($entry_types) === 0) {
            $errors['entry_types_empty'] = "Please add at least one entry type";
        } else {
            foreach ($entry_types as $index => $entry_type) {
                if (empty($entry_type) || !in_array($entry_type, ['debit', 'credit'])) {
                    $errors["entry_type_{$index}_invalid"] = "Please select a valid entry type for line " . ($index + 1);
                }
            }
        }

        return $errors;
    }
}