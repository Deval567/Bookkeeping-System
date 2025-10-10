<?php
class Users
{

    public $username;
    public $password;
    public $role;
    protected $conn;

    public function __construct($conn, $username, $password, $role)
    {
        $this->conn = $conn;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
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
    public function createUser()
    {
        $exists = $this->isUserExists();
        if ($exists) {
            $_SESSION['user_errors'] = ["User already exists."];
        } else {
            $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($this->conn);  if (!mysqli_stmt_prepare($stmt, $sql)) {
                return false;
            }

            mysqli_stmt_bind_param($stmt, "sss", $this->username, $this->password, $this->role);
            return mysqli_stmt_execute($stmt);
        }
    }
}
