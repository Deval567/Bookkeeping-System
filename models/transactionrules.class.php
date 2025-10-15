<?php
class transactionRules
{

    public $id;
    public $conn;
    public $rule_name;
    public $category;
    public $debit_account;
    protected $credit_account;
    protected $description;
    private $limit = 10;

    public function __construct($conn, $id, $rule_name, $category, $debit_account, $credit_account, $description)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->rule_name = $rule_name;
        $this->category = $category;
        $this->debit_account = $debit_account;
        $this->credit_account = $credit_account;
        $this->description = $description;
    }
    public function getTotalRules($search = '', $categoryFilter = '', $debitAccountFilter = '', $creditAccountFilter = '')
    {
        $search = trim($search);
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(rule_name LIKE '%$search%' OR category LIKE '%$search%' OR description LIKE '%$search%')";
        }

        if ($categoryFilter !== '') {
            $categoryFilter = mysqli_real_escape_string($this->conn, $categoryFilter);
            $conditions[] = "category = '$categoryFilter'";
        }

        if ($debitAccountFilter !== '') {
            $debitAccountFilter = mysqli_real_escape_string($this->conn, $debitAccountFilter);
            $conditions[] = "debit_account_id = $debitAccountFilter";
        }

        if ($creditAccountFilter !== '') {
            $creditAccountFilter = mysqli_real_escape_string($this->conn, $creditAccountFilter);
            $conditions[] = "credit_account_id = $creditAccountFilter";
        }

        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) AS total FROM transaction_rules $whereClause";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }
    public function getPaginatedRules($page = 1, $search = '', $categoryFilter = '', $debitAccountFilter = '', $creditAccountFilter = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' ||  $categoryFilter !== '' || $debitAccountFilter !== '' || $creditAccountFilter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(tr.rule_name LIKE '%$search%' OR tr.category LIKE '%$search%' OR tr.description LIKE '%$search%' OR da.account_name LIKE '%$search%' OR ca.account_name LIKE '%$search%')";
            }

            if ($categoryFilter !== '') {
                $categoryFilter = mysqli_real_escape_string($this->conn, $categoryFilter);
                $conditions[] = "category = '$categoryFilter'";
            }

            if ($debitAccountFilter !== '') {
                $debitAccountFilter = mysqli_real_escape_string($this->conn, $debitAccountFilter);
                $conditions[] = "debit_account_id = $debitAccountFilter";
            }

            if ($creditAccountFilter !== '') {
                $creditAccountFilter = mysqli_real_escape_string($this->conn, $creditAccountFilter);
                $conditions[] = "credit_account_id = $creditAccountFilter";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql ="SELECT 
            tr.id,
            tr.rule_name,
            tr.category,
            tr.debit_account_id,
            tr.credit_account_id,
            tr.description,
            da.account_name AS debit_account_name,
            ca.account_name AS credit_account_name
        FROM transaction_rules tr
        LEFT JOIN chart_of_accounts da ON tr.debit_account_id = da.id
        LEFT JOIN chart_of_accounts ca ON tr.credit_account_id = ca.id
        $filterQuery 
        ORDER BY tr.category ASC 
        LIMIT {$this->limit} OFFSET {$offset}";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalPages($search = '', $categoryFilter = '', $debitAccountFilter = '', $creditAccountFilter = '')
    {
        $totalRules = $this->getTotalRules($search, $categoryFilter, $debitAccountFilter, $creditAccountFilter);
        return ceil($totalRules / $this->limit);
    }
}
