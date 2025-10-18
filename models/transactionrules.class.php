<?php
class transactionRules
{

    public $id;
    public $conn;
    public $rule_name;
    public $category;
    protected $description;
    private $limit = 10;

    public function __construct($conn, $id, $rule_name, $category, $description)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->rule_name = $rule_name;
        $this->category = $category;
        $this->description = $description;
    }
    public function getAllRules()
    {
        $sql = "SELECT * FROM transaction_rules ORDER BY category ASC";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalRules($search = '', $categoryFilter = '')
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

        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) AS total FROM transaction_rules $whereClause";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }
    public function getPaginatedRules($page = 1, $search = '', $categoryFilter = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' ||  $categoryFilter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(rule_name LIKE '%$search%' OR category LIKE '%$search%' OR description LIKE '%$search%')";
            }

            if ($categoryFilter !== '') {
                $categoryFilter = mysqli_real_escape_string($this->conn, $categoryFilter);
                $conditions[] = "category = '$categoryFilter'";
            }


            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT * FROM transaction_rules
        $filterQuery 
        ORDER BY category ASC 
        LIMIT {$this->limit} OFFSET {$offset}";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalPages($search = '', $categoryFilter = '')
    {
        $totalRules = $this->getTotalRules($search, $categoryFilter);
        return ceil($totalRules / $this->limit);
    }
  public function createTransactionRule($rule_name, $category,$description)
{
    $sql = "INSERT INTO transaction_rules (rule_name, category,  description) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($this->conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sss", $rule_name, $category, $description);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
    public function deleteTransactionRule($id)
    {
        $sql = "DELETE FROM transaction_rules WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function updateTransactionRule($id, $rule_name, $category, $description)
    {
        $sql = "UPDATE transaction_rules SET rule_name = ?, category = ?, description = ? WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi", $rule_name, $category, $description, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
    public function getRuleNamebyId($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);

        $sql = "SELECT rule_name FROM transaction_rules WHERE id = '$id'";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row ? $row['rule_name'] : null;
    }
}
