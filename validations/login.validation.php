<?php

function is_username_empty($username)
{

    if (empty($username)) {
        return true;
    }else{
        return false;
    }
}
function is_password_empty($password)
{

    if (empty($password)) {
        return true;
    }else{
        return false;
    }
}
function is_username_invalid($username)
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return true;
    }
    return false;
}

function is_username_length_invalid($username)
{
    return strlen($username) < 6;
}

function is_password_length_invalid($password)
{
    return strlen($password) < 6;
}

?>