<?php
class TransactionRuleLines
{
    public $id;
    public $conn;
    public $rule_id;
    public $account_id;
    public $entry_type;
    private $limit = 10;

    public function __construct($conn, $id, $rule_id, $account_id, $entry_type)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->rule_id = $rule_id;
        $this->account_id = $account_id;
        $this->entry_type = $entry_type;
    }
    public function getRuleIdRulelinesGroupedByCategory()
    {
        $sql = "
        SELECT tr.category, tr.id AS rule_id, tr.rule_name, GROUP_CONCAT(trl.id ORDER BY trl.id) AS rule_line_ids
        FROM transaction_rule_lines trl
        JOIN transaction_rules tr ON tr.id = trl.rule_id
        GROUP BY tr.category, tr.id, tr.rule_name
        ORDER BY tr.category ASC, tr.rule_name ASC
    ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalRuleLines($search = '', $rule_id = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' ||  $rule_id !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(tr.rule_name LIKE '%$search%' 
                          OR coa.account_name LIKE '%$search%' 
                          )";
            }

            if ($rule_id !== '') {
                $rule_id = mysqli_real_escape_string($this->conn, $rule_id);
                $conditions[] = "trl.rule_id = '$rule_id'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "
    SELECT COUNT(*) AS total
    FROM transaction_rule_lines AS trl
    JOIN transaction_rules AS tr ON trl.rule_id = tr.id
    JOIN chart_of_accounts AS coa ON trl.account_id = coa.id
    $filterQuery";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedRuleLines($page = 1, $search = '', $rule_id = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(tr.rule_name LIKE '%$search%' 
                  OR coa.account_name LIKE '%$search%' 
                  )";
        }

        if ($rule_id !== '') {
            $rule_id = mysqli_real_escape_string($this->conn, $rule_id);
            $conditions[] = "trl.rule_id = '$rule_id'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : '';

        $sql = "
    SELECT
        trl.id,
        trl.rule_id,
        tr.rule_name,
        coa.id AS account_id,
        coa.account_name,
        trl.entry_type
    FROM transaction_rule_lines AS trl
    JOIN transaction_rules AS tr ON trl.rule_id = tr.id
    JOIN chart_of_accounts AS coa ON trl.account_id = coa.id
    $filterQuery
    ORDER BY tr.rule_name ASC
    LIMIT {$this->limit} OFFSET {$offset}";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalPages($search = '', $rule_id = '')
    {
        return ceil($this->getTotalRuleLines($search,  $rule_id) / $this->limit);
    }

    public function isRuleLineExists($rule_id, $account_id, $entry_type, $exclude_id = null)
    {
        $sql = "SELECT id FROM transaction_rule_lines 
            WHERE rule_id = ? 
              AND account_id = ? 
              AND entry_type = ?";

        // Exclude current row if updating
        if ($exclude_id !== null) {
            $sql .= " AND id != ?";
        }

        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) return false;

        if ($exclude_id !== null) {
            mysqli_stmt_bind_param($stmt, "iisi", $rule_id, $account_id, $entry_type, $exclude_id);
        } else {
            mysqli_stmt_bind_param($stmt, "iis", $rule_id, $account_id, $entry_type);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        return mysqli_stmt_num_rows($stmt) > 0;
    }

    public function createTransactionRuleLine($rule_id, $account_id, $entry_type)
    {

        $sql = "INSERT INTO transaction_rule_lines (rule_id, account_id, entry_type) 
                VALUES (?,?,?)";

        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "iis", $rule_id, $account_id, $entry_type);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
    public function deleteTransactionRuleLine($conn, $id)
    {
        $sql = "DELETE FROM transaction_rule_lines WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return ['success' => false, 'error' => 'prepare_failed'];
        }

        mysqli_stmt_bind_param($stmt, "i", $id);

        try {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            mysqli_stmt_close($stmt);
            // Any error = rule line is in use
            return ['success' => false, 'error' => 'rule_line_in_use'];
        }
    }
    public function updateTransactionRuleLine($id, $rule_id, $account_id, $entry_type)
    {
        $sql = "UPDATE transaction_rule_lines 
                SET rule_id = ?, account_id = ?, entry_type = ? 
                WHERE id = ?";

        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "iisi", $rule_id, $account_id, $entry_type, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
    public function getRuleLineById($id)
    {
        $sql = "SELECT * FROM transaction_rule_lines WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function getRuleLinesByRuleId($rule_id)
    {
        $sql = "SELECT * FROM transaction_rule_lines WHERE rule_id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, "i", $rule_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $lines = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $lines;
    }
}
