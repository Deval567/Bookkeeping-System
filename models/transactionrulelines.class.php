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
    public function getRuleIdRulelinesGroupByRuleNames()
    {
        $sql = "
        SELECT trl.rule_id, tr.rule_name, GROUP_CONCAT(trl.id ORDER BY trl.id) AS rule_line_ids
        FROM transaction_rule_lines trl
        JOIN transaction_rules tr ON tr.id = trl.rule_id
        GROUP BY trl.rule_id, tr.rule_name
        ORDER BY tr.rule_name ASC";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalRuleLines($search = '', $entry_type = '', $rule_id = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $entry_type !== '' || $rule_id !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(rule_name LIKE '%$search%' 
                              OR account_name LIKE '%$search%' 
                              OR entry_type LIKE '%$search%')";
            }

            if ($entry_type !== '') {
                $entry_type = mysqli_real_escape_string($this->conn, $entry_type);
                $conditions[] = "entry_type = '$entry_type'";
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

    public function getPaginatedRuleLines($page = 1, $search = '', $entry_type = '', $rule_id = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $entry_type !== '' || $rule_id !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(rule_name LIKE '%$search%' 
                              OR account_name LIKE '%$search%' 
                              OR entry_type LIKE '%$search%')";
            }

            if ($entry_type !== '') {
                $entry_type = mysqli_real_escape_string($this->conn, $entry_type);
                $conditions[] = "entry_type = '$entry_type'";
            }
            if ($rule_id !== '') {
                $rule_id = mysqli_real_escape_string($this->conn, $rule_id);
                $conditions[] = "trl.rule_id = '$rule_id'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

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

    public function getTotalPages($search = '', $filter = '')
    {
        return ceil($this->getTotalRuleLines($search, $filter) / $this->limit);
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
    public function deleteTransactionRuleLine($id)
    {
        $sql = "DELETE FROM transaction_rule_lines WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
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
}
