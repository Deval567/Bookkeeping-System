<?php

class Transaction
{
    private $id;
    private $conn;
    private $transaction_id;
    private $transaction_date;
    private $reference_number;
    private $entry_type;
    private $total_amount;
    private $encoded_by;
    private $description;
    private $limit = 10;


    public function __construct($conn, $rule_id, $transaction_date, $reference_number, $description, $entry_type, $total_amount, $encoded_by)
    {
        $this->conn = $conn;
        $this->id = $rule_id;
        $this->transaction_date = $transaction_date;
        $this->reference_number = $reference_number;
        $this->description = $description;
        $this->entry_type = $entry_type;
        $this->total_amount = $total_amount;
        $this->encoded_by = $encoded_by;
    }
    public function getTransactionsGroupByRuleName()
    {
        $sql = "
        SELECT t.rule_id, tr.rule_name, GROUP_CONCAT(t.id ORDER BY t.id) AS transaction_ids
        FROM transactions t
        JOIN transaction_rules tr ON tr.id = t.rule_id
        GROUP BY t.rule_id, tr.rule_name
        ORDER BY tr.rule_name ASC
    ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTransactionsGroupedByUser()
    {
        $sql = "
        SELECT 
            u.id AS user_id,
            u.username,
            GROUP_CONCAT(t.id ORDER BY t.id) AS transaction_ids
        FROM transactions t
        JOIN users u ON t.created_by = u.id
        JOIN transaction_rules tr ON tr.id = t.rule_id
        GROUP BY u.id, u.username
        ORDER BY u.username ASC
    ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalTransactions($search = '', $filterRuleId = '', $filterUsername = '', $filterDateFrom = '', $filterDateTo = '')
    {
        $search = trim($search);
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(t.reference_no LIKE '%$search%'
                    OR tr.rule_name LIKE '%$search%'
                    OR t.description LIKE '%$search%'
                    OR u.username LIKE '%$search%')";
        }

        if ($filterRuleId !== '') {
            $filterRuleId = mysqli_real_escape_string($this->conn, $filterRuleId);
            $conditions[] = "t.rule_id = '$filterRuleId'";
        }

        if ($filterUsername !== '') {
            $filterUsername = mysqli_real_escape_string($this->conn, $filterUsername);
            $conditions[] = "u.username = '$filterUsername'";
        }

        if ($filterDateFrom !== '' && $filterDateTo !== '') {
            $filterDateFrom = mysqli_real_escape_string($this->conn, $filterDateFrom);
            $filterDateTo = mysqli_real_escape_string($this->conn, $filterDateTo);
            $conditions[] = "DATE(t.transaction_date) BETWEEN '$filterDateFrom' AND '$filterDateTo'";
        } elseif ($filterDateFrom !== '') {
            $filterDateFrom = mysqli_real_escape_string($this->conn, $filterDateFrom);
            $conditions[] = "DATE(t.transaction_date) >= '$filterDateFrom'";
        } elseif ($filterDateTo !== '') {
            $filterDateTo = mysqli_real_escape_string($this->conn, $filterDateTo);
            $conditions[] = "DATE(t.transaction_date) <= '$filterDateTo'";
        }

        $filterQuery = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT COUNT(*) AS total
            FROM transactions AS t
            JOIN transaction_rules AS tr ON t.rule_id = tr.id
            JOIN users AS u ON t.created_by = u.id
            $filterQuery
        ";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedTransactions($page = 1, $search = '', $filterRuleId = '', $filterUsername = '', $filterDateFrom = '', $filterDateTo = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(t.reference_no LIKE '%$search%'
                OR tr.rule_name LIKE '%$search%'
                OR t.description LIKE '%$search%'
                OR u.username LIKE '%$search%')";
        }

        if ($filterRuleId !== '') {
            $filterRuleId = mysqli_real_escape_string($this->conn, $filterRuleId);
            $conditions[] = "t.rule_id = '$filterRuleId'";
        }

        if ($filterUsername !== '') {
            $filterUsername = mysqli_real_escape_string($this->conn, $filterUsername);
            $conditions[] = "u.id = '$filterUsername'";
        }

        if ($filterDateFrom !== '' && $filterDateTo !== '') {
            $filterDateFrom = mysqli_real_escape_string($this->conn, $filterDateFrom);
            $filterDateTo = mysqli_real_escape_string($this->conn, $filterDateTo);
            $conditions[] = "DATE(t.transaction_date) BETWEEN '$filterDateFrom' AND '$filterDateTo'";
        } elseif ($filterDateFrom !== '') {
            $filterDateFrom = mysqli_real_escape_string($this->conn, $filterDateFrom);
            $conditions[] = "DATE(t.transaction_date) >= '$filterDateFrom'";
        } elseif ($filterDateTo !== '') {
            $filterDateTo = mysqli_real_escape_string($this->conn, $filterDateTo);
            $conditions[] = "DATE(t.transaction_date) <= '$filterDateTo'";
        }

        $filterQuery = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : '';

        $sql = "
        SELECT t.id, t.reference_no, t.description, t.total_amount, t.transaction_date, t.rule_id, tr.rule_name, u.username
        FROM transactions AS t
        JOIN transaction_rules AS tr ON t.rule_id = tr.id
        JOIN users AS u ON t.created_by = u.id
        $filterQuery
        ORDER BY t.transaction_date DESC
        LIMIT {$this->limit} OFFSET {$offset}
    ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    public function getransactionRuleIdById($id)
    {
        $sql = "SELECT rule_id FROM transactions WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function getTotalTransactionPages($search = '', $filterRuleId = '', $filterUsername = '', $filterDateFrom = '', $filterDateTo = '')
    {
        return ceil($this->getTotalTransactions($search, $filterRuleId, $filterUsername, $filterDateFrom, $filterDateTo) / $this->limit);
    }
    public function createTransaction($rule_id, $reference_no, $description, $transaction_date, $total_amount, $created_by)
    {
        $sql = "INSERT INTO transactions (rule_id, reference_no, description, transaction_date, total_amount, created_by) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        mysqli_stmt_bind_param($stmt, 'isssdi', $rule_id, $reference_no, $description, $transaction_date, $total_amount, $created_by);

        if (mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->conn);
        } else {
            return false;
        }
    }
    public function deleteTransaction($id)
    {
        $sql = "DELETE FROM transactions WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt);
    }
    public function updateTransaction($id, $rule_id, $reference_no, $description, $transaction_date, $total_amount)
    {
        $sql = "UPDATE transactions 
            SET rule_id = ?, reference_no = ?, description = ?, transaction_date = ?, total_amount = ? 
            WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssdi", $rule_id, $reference_no, $description, $transaction_date, $total_amount, $id);
        return mysqli_stmt_execute($stmt);
    }
}
