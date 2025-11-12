<?php

class TransactionRuleLinesValidation
{
    public function validate($rule_id, $account_ids, $entry_types)
    {
        $errors = [];

        $account_ids = isset($account_ids) ? (array) $account_ids : [];
        $entry_types = isset($entry_types) ? (array) $entry_types : [];

        $account_ids = array_map('trim', $account_ids);
        $entry_types = array_map('trim', $entry_types);

        if (empty($rule_id)) {
            $errors['rule_id_empty'] = "Please select a Transaction Rule";
        }

        if (count($account_ids) === 0) {
            $errors['account_ids_empty'] = "Please add at least one account line";
        } else {
            foreach ($account_ids as $i => $account) {
                if (empty($account)) {
                    $errors["account_id_{$i}_empty"] = "Please select an account for line " . ($i + 1);
                }
            }
        }

        if (count($entry_types) === 0) {
            $errors['entry_types_empty'] = "Please add at least one entry type";
        } else {
            foreach ($entry_types as $i => $type) {
                $type_lower = strtolower($type);
                if (empty($type) || !in_array($type_lower, ['debit', 'credit'])) {
                    $errors["entry_type_{$i}_invalid"] = "Please select a valid entry type (Debit or Credit) for line " . ($i + 1);
                }
            }
        }

        if (count($entry_types) > 1) {
            $has_debit = false;
            $has_credit = false;
            foreach ($entry_types as $type) {
                $type_lower = strtolower($type);
                if ($type_lower === 'debit') $has_debit = true;
                if ($type_lower === 'credit') $has_credit = true;
            }
            if (!$has_debit || !$has_credit) {
                $errors['entry_types_incomplete'] = "Transaction must have at least one debit and one credit entry";
            }
        }

        $non_empty_accounts = array_filter($account_ids, fn($a) => !empty($a));
        if (count($non_empty_accounts) !== count(array_unique($non_empty_accounts))) {
            $errors['duplicate_accounts'] = "Duplicate accounts are not allowed. Each account can only appear once in the transaction rule";
        }

        return $errors;
    }
}
