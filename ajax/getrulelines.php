<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once '../configs/dbc.php';

header('Content-Type: application/json');

if (isset($_POST['rule_id'])) {
    $rule_id = intval($_POST['rule_id']);
    $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : null;

    $stmt = $conn->prepare("
        SELECT trl.account_id, coa.account_name, trl.entry_type
        FROM transaction_rule_lines AS trl
        JOIN chart_of_accounts AS coa ON trl.account_id = coa.id
        WHERE trl.rule_id = ?
    ");
    
    if (!$stmt) {
        echo json_encode(['error' => 'Database error']);
        exit;
    }
    
    $stmt->bind_param('i', $rule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lines = [];
    while ($row = $result->fetch_assoc()) {
        $lines[] = $row;
    }

    if ($transaction_id && !empty($lines)) {
        $stmt = $conn->prepare("
            SELECT account_id, 
                   CASE 
                       WHEN debit > 0 THEN debit 
                       WHEN credit > 0 THEN credit 
                       ELSE 0 
                   END as amount
            FROM journal_entries
            WHERE transaction_id = ?
        ");
        
        if ($stmt) {
            $stmt->bind_param('i', $transaction_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $amounts = [];
            while ($row = $result->fetch_assoc()) {
                $amounts[$row['account_id']] = $row['amount'];
            }

            foreach ($lines as &$line) {
                $line['amount'] = $amounts[$line['account_id']] ?? '';
            }
        }
    }

    echo json_encode($lines);
} else {
    echo json_encode([]);
}

exit;