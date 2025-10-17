<?php
class transactionRulesValidation
{
    public function validate($rule_name, $category, $description)
    {
        $errors = [];

        if (empty($rule_name)) {
            $errors[] = "Rule name is required.";
        } elseif (strlen($rule_name) > 100) {
            $errors[] = "Rule name must not exceed 100 characters.";
        }

        if (empty($category)) {
            $errors[] = "Category is required.";
        } elseif (strlen($category) > 50) {
            $errors[] = "Category must not exceed 50 characters.";
        }

        if (!empty($description) && strlen($description) > 255) {
            $errors[] = "Description must not exceed 255 characters.";
        }

        return $errors;
    }
}
