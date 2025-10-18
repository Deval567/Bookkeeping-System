<?php

class ChartofAccounts
{
    private $conn;
    private $id;
    private $account_name;
    private $account_type;
    private $description;
    private $limit = 10;

    public function __construct($conn, $id, $account_name, $account_type, $description)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->account_name = $account_name;
        $this->account_type = $account_type;
        $this->description = $description;
    }
    public function getAllChart()
    {
        $sql = "SELECT * FROM chart_of_accounts ORDER BY account_type ASC";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalCharts($search = '', $filter = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(account_name LIKE '%$search%' 
                              OR account_type LIKE '%$search%' 
                              OR description LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "account_type = '$filter'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) AS total FROM chart_of_accounts $filterQuery";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedCharts($page = 1, $search = '', $filter = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(account_name LIKE '%$search%' 
                              OR account_type LIKE '%$search%' 
                              OR description LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "account_type = '$filter'";
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

    public function getTotalPages($search = '', $filter = '')
    {
        return ceil($this->getTotalCharts($search, $filter) / $this->limit);
    }

    public function isAccountExists()
    {
        $sql = "SELECT * FROM chart_of_accounts WHERE account_name = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['login_errors'] = ["Something went wrong. Please try again later."];
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $this->account_name);
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
        $sql = "INSERT INTO chart_of_accounts (account_name, account_type, description) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sss', $this->account_name, $this->account_type, $this->description);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function updateAccount($id, $account_name, $account_type, $description)
    {
        $sql = "UPDATE chart_of_accounts SET account_name = ?, account_type = ?, description = ? WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi", $account_name, $account_type, $description, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function deleteAccount($id)
    {
        $sql = "DELETE FROM chart_of_accounts WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
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
