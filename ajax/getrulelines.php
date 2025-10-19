<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../configs/dbc.php';

if (isset($_POST['rule_id'])) {
    $rule_id = intval($_POST['rule_id']);

    $sql = "SELECT coa.account_name, trl.entry_type
            FROM transaction_rule_lines AS trl
            JOIN chart_of_accounts AS coa ON trl.account_id = coa.id
            WHERE trl.rule_id = $rule_id";

    $result = $conn->query($sql);
    $lines = [];

    while ($row = $result->fetch_assoc()) {
        $lines[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($lines);
}
