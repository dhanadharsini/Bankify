<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT name FROM users WHERE id = $id");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['name'];
    } else {
        echo ""; // No user found
    }
}
$conn->close();
?>
