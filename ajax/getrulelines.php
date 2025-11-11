<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once '../configs/dbc.php';
header('Content-Type: application/json');

if (isset($_POST['rule_id'])) {
    $rule_id = intval($_POST['rule_id']);
    $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : null;

    // Fetch rule lines (with their rule_line_id)
    $stmt = $conn->prepare("
        SELECT 
            trl.id AS rule_line_id, 
            trl.account_id, 
            coa.account_name, 
            trl.entry_type
        FROM transaction_rule_lines AS trl
        JOIN chart_of_accounts AS coa ON trl.account_id = coa.id
        WHERE trl.rule_id = ?
    ");
    
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare error']);
        exit;
    }
    
    $stmt->bind_param('i', $rule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lines = [];
    while ($row = $result->fetch_assoc()) {
        $lines[] = $row;
    }
    $stmt->close();

    // If transaction_id is provided, fetch existing amounts
    if ($transaction_id && !empty($lines)) {
        $stmt = $conn->prepare("
            SELECT 
                rule_line_id,
                CASE 
                    WHEN debit > 0 THEN debit 
                    WHEN credit > 0 THEN credit 
                    ELSE 0 
                END AS amount
            FROM journal_entries
            WHERE transaction_id = ?
        ");
        
        if ($stmt) {
            $stmt->bind_param('i', $transaction_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $amounts = [];
            while ($row = $result->fetch_assoc()) {
                $amounts[$row['rule_line_id']] = $row['amount'];
            }

            // Match the amounts to their rule_line_id
            foreach ($lines as &$line) {
                $line['amount'] = $amounts[$line['rule_line_id']] ?? '';
            }
            $stmt->close();
        }
    }

    echo json_encode($lines);
} else {
    echo json_encode([]);
}

exit;
?>
