<?php
class UserValidation
{
    public function validate($username, $password,$roles)
    {
        $errors = [];

        if(empty($roles)){
            $errors['role_empty'] = "Please select a role";
        }

        if (empty($username)) {
            $errors['username_empty'] = "Please fill the Username field";
        }

        if (empty($password)) {
            $errors['password_empty'] = "Please fill the Password field";
        }

        if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['invalid_username'] = "Please enter a valid username";
        }

        if (!empty($username) && strlen($username) < 6) {
            $errors['username_length'] = "Username must be at least 6 characters long";
        }

        if (!empty($password) && strlen($password) < 6) {
            $errors['password_length'] = "Password must be at least 6 characters long";
        }

        return $errors;
    }
}
?>
