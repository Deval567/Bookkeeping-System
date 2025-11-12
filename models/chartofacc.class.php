<?php

class ChartofAccounts
{
    private $conn;
    private $id;
    private $account_name;
    private $account_type;
    private $cash_flow_category;
    private $description;
    private $limit = 10;

    public function __construct($conn, $id, $account_name, $account_type, $cash_flow_category, $description)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->account_name = $account_name;
        $this->account_type = $account_type;
        $this->cash_flow_category = $cash_flow_category;
        $this->description = $description;
    }
    public function getAllChart()
    {
        $sql = "
        SELECT 
            id,
            account_name,
            account_type,
            cash_flow_category
        FROM chart_of_accounts
        ORDER BY account_type ASC, account_name ASC
    ";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalCharts($search = '', $filter = '', $cash_flow_category = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '' || $cash_flow_category !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(account_name LIKE '%$search%' 
                          OR account_type LIKE '%$search%'
                          OR cash_flow_category LIKE '%$search%'
                          OR description LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "account_type = '$filter'";
            }

            if ($cash_flow_category !== '') {
                $cash_flow_category = mysqli_real_escape_string($this->conn, $cash_flow_category);
                $conditions[] = "cash_flow_category = '$cash_flow_category'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) AS total FROM chart_of_accounts $filterQuery";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedCharts($page = 1, $search = '', $filter = '', $cash_flow_category = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        // FIXED: Added $cash_flow_category to the condition
        if ($search !== '' || $filter !== '' || $cash_flow_category !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(account_name LIKE '%$search%' 
                          OR account_type LIKE '%$search%' 
                          OR cash_flow_category LIKE '%$search%'
                          OR description LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "account_type = '$filter'";
            }

            if ($cash_flow_category !== '') {
                $cash_flow_category = mysqli_real_escape_string($this->conn, $cash_flow_category);
                $conditions[] = "cash_flow_category = '$cash_flow_category'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "
        SELECT * 
        FROM chart_of_accounts 
        $filterQuery
        ORDER BY account_type ASC 
        LIMIT {$this->limit} OFFSET {$offset}";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalPages($search = '', $filter = '', $cash_flow_category = '')
    {
        return ceil($this->getTotalCharts($search, $filter, $cash_flow_category) / $this->limit);
    }
    public function isAccountExists()
    {
        $sql = "SELECT * FROM chart_of_accounts WHERE account_name = ? AND account_type = ? AND cash_flow_category = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['login_errors'] = ["Something went wrong. Please try again later."];
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sss", $this->account_name, $this->account_type, $this->cash_flow_category);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);



        if ($row = mysqli_fetch_assoc($result)) {
            return $row; // return the user row if found
        } else {
            return false; // return false if no user found
        }
    }

    public function createAccount()
    {
        $sql = "INSERT INTO chart_of_accounts (account_name, account_type, cash_flow_category, description) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssss', $this->account_name, $this->account_type, $this->cash_flow_category, $this->description);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function updateAccount($id, $account_name, $account_type, $cash_flow_category, $description)
    {
        $sql = "UPDATE chart_of_accounts SET account_name = ?, account_type = ?, cash_flow_category = ?, description = ? WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ssssi", $account_name, $account_type, $cash_flow_category, $description, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function deleteAccount($conn, $id)
    {
        $sql = "DELETE FROM chart_of_accounts WHERE id = ?";
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
            return ['success' => false, 'error' => 'account_in_use'];
        }
    }
    public function getAccountNamebyId($id)
    {
        $sql = "SELECT account_name FROM chart_of_accounts WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row['account_name'];
        } else {
            return false;
        }
    }
}
