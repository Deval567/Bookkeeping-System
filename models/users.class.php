<?php
class Users
{

    public $username;
    public $password;
    public $role;
    protected $conn;
    public $id;

    public function __construct($conn, $id, $username, $password, $role)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }
    public function showAllUsers()
    {
        $sql = "SELECT * FROM users";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
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
            return $row; // return the user row if found
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

            mysqli_stmt_bind_param($stmt, "sss",$username, $password, $role);
            return mysqli_stmt_execute($stmt);
        
    }
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
    public function updateUser($id,$conn ,$username, $password, $role)
    {
        $sql = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $role, $id);
        return mysqli_stmt_execute($stmt);
    }
}
