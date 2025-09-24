<?php
$host = "localhost";
$user = "root";
$pass = "Dhanas@3112";
$dbname = "bank_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>