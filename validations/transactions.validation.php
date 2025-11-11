<?php

class TransactionsValidation {

    public function validate($rule_id, $date, $reference_no, $total_amount, $description, $rule_line_ids, $amounts) {
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

        // Entries validation (rule_line_ids and amounts)
        if (empty($rule_line_ids) || empty($amounts)) {
            $errors['entries_empty'] = "Please provide at least one transaction entry.";
        } else {
            // Check if rule_line_ids and amounts have matching counts
            if (count($rule_line_ids) !== count($amounts)) {
                $errors['entries_mismatch'] = "Number of rule lines and amounts do not match.";
                return $errors;
            }

            $total_debit = 0;
            $total_credit = 0;

            // We need to fetch entry types from the database to validate balance
            // This requires database connection
            global $conn;
            if (isset($conn)) {
                foreach ($rule_line_ids as $index => $rule_line_id) {
                    $amount = $amounts[$index];

                    // Check if valid amount
                    if (!is_numeric($amount) || $amount <= 0) {
                        $errors["amount_invalid_$index"] = "Amount for entry " . ($index + 1) . " must be a positive number.";
                    }

                    // Validate rule_line_id exists and get entry_type
                    if (!empty($rule_line_id) && is_numeric($rule_line_id)) {
                        $stmt = $conn->prepare("SELECT entry_type FROM transaction_rule_lines WHERE id = ?");
                        $stmt->bind_param("i", $rule_line_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            $rule_line = $result->fetch_assoc();
                            $entry_type = $rule_line['entry_type'];

                            // Sum debit or credit based on entry type
                            if ($entry_type === 'debit') {
                                $total_debit += floatval($amount);
                            } elseif ($entry_type === 'credit') {
                                $total_credit += floatval($amount);
                            }
                        } else {
                            $errors["rule_line_invalid_$index"] = "Rule line ID " . ($index + 1) . " is invalid.";
                        }
                        $stmt->close();
                    } else {
                        $errors["rule_line_empty_$index"] = "Please select a rule line for entry " . ($index + 1) . ".";
                    }
                }

                // Check if debit and credit are balanced
                if (abs($total_debit - $total_credit) > 0.01) { // Using small epsilon for float comparison
                    $errors['balance_error'] = "Total Debit (" . number_format($total_debit, 2) . ") must equal Total Credit (" . number_format($total_credit, 2) . ").";
                }

                // Check if total amount matches the debit/credit total
                if (abs(floatval($total_amount) - $total_debit) > 0.01) { // Using small epsilon for float comparison
                    $errors['total_mismatch'] = "Total Amount (" . number_format($total_amount, 2) . ") must equal total debit/credit (" . number_format($total_debit, 2) . ").";
                }
            }
        }

        return $errors;
    }
}

?>