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
     public function getTotalRuleLines($search = '', $filter = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(rule_name LIKE '%$search%' 
                              OR account_name LIKE '%$search%' 
                              OR entry_type LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "entry_type = '$filter'";
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

    public function getPaginatedRuleLines($page = 1, $search = '', $filter = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(rule_name LIKE '%$search%' 
                              OR account_name LIKE '%$search%' 
                              OR entry_type LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "entry_type = '$filter'";
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
}