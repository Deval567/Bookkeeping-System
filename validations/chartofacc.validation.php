<?php 

class ChartofAccountValidation
{
    public function validate($account_name, $account_type, $description)
    {
        $errors = [];

        if (empty($account_name)) {
            $errors[] = "Account name is required.";
        } elseif (strlen($account_name) > 100) {
            $errors[] = "Account name must not exceed 100 characters.";
        }

        if (empty($account_type)) {
            $errors[] = "Account type is required.";
        } elseif (!in_array($account_type, ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'])) {
            $errors[] = "Invalid account type selected.";
        }

        if (!empty($description) && strlen($description) > 255) {
            $errors[] = "Description must not exceed 255 characters.";
        }

        return $errors;
    }
}