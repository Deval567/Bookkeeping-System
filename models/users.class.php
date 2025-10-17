<?php
class Users
{

    public $username;
    public $password;
    public $role;
    protected $conn;
    public $id;
    private $limit = 10;

    public function __construct($conn, $id, $username, $password, $role)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }
    public function getTotalUsers($search = '', $filter = '')
    {
        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(username LIKE '%$search%' 
                              OR role LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "role = '$filter'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) AS total FROM users $filterQuery";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedUsers($page = 1, $search = '', $filter = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;

        $search = trim($search);
        $filterQuery = '';

        if ($search !== '' || $filter !== '') {
            $conditions = [];

            if ($search !== '') {
                $search = mysqli_real_escape_string($this->conn, $search);
                $conditions[] = "(username LIKE '%$search%' 
                              OR role LIKE '%$search%')";
            }

            if ($filter !== '') {
                $filter = mysqli_real_escape_string($this->conn, $filter);
                $conditions[] = "role = '$filter'";
            }

            $filterQuery = "WHERE " . implode(' AND ', $conditions);
        }

        $sql = "
        SELECT * 
        FROM users 
        $filterQuery
        ORDER BY role ASC 
        LIMIT {$this->limit} OFFSET {$offset}";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getTotalPages($search = '', $filter = '')
    {
        return ceil($this->getTotalUsers($search, $filter) / $this->limit);
    }



    public function isUserExists()
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['login_errors'] = ["Something went wrong. Please try again later."];
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $this->username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);


        if ($row = mysqli_fetch_assoc($result)) {
            return $row;
        } else {
            return false;
        }
    }
    public function createUser($username, $password, $role)
    {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function updateUser($id, $username, $password, $role)
    {
        $sql = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $role, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
}
