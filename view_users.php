<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <h2>All Users</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Balance</th>
            </tr>
            <?php
            require 'db.php';
            $result = $conn->query("SELECT * FROM users");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>₹{$row['balance']}</td>
                      </tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
