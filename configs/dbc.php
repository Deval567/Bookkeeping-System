<?php

$dbhost = "localhost";
$dbusername = "root";
$dbpasss = "";
$dbname= "bookkeepingsystem";

$conn= mysqli_connect($dbhost,$dbusername,$dbpasss,$dbname);

if(!$conn){
    die("Connection Failed: Please try again later" . mysqli_connect_error());
}

?>