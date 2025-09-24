<?php
require 'db.php';

$id = $_POST['id'];

// Get user name
$userResult = $conn->prepare("SELECT name FROM users WHERE id = ?");
$userResult->bind_param("i", $id);
$userResult->execute();
$userData = $userResult->get_result();

if ($userData->num_rows === 0) {
    die("User not found.");
}
$user = $userData->fetch_assoc()['name'];

// Call stored procedure to get transaction data
$statement = $conn->prepare("CALL GetBankStatement(?)");
$statement->bind_param("i", $id);
$statement->execute();
$result = $statement->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Statement</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #ffffff10;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #0077b6;
            color: white;
        }
        h2, p {
            text-align: center;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #00b4d8;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0077b6;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Bank Statement</h2>
        <p><strong>User:</strong> <?= htmlspecialchars($user) ?> (ID: <?= $id ?>)</p>

        <table>
            <thead>
                <tr>
                    <th>Tran Date</th>
                    <th>Particulars</th>
                    <th>Deposit</th>
                    <th>Withdraw</th>
                    <th>Balance</th> <!-- New Column -->
                </tr>
            </thead>
            <tbody>
                <?php
                $balance = 0;
                while ($row = $result->fetch_assoc()) {
                    $deposit = (float)$row['deposit'];
                    $withdraw = (float)$row['withdraw'];
                    $balance += $deposit - $withdraw;
                ?>
                    <tr>
                        <td><?= $row['tran_date'] ?? '-' ?></td>
                        <td><?= $row['Particulars'] ?></td>
                        <td>₹<?= $deposit ?></td>
                        <td>₹<?= $withdraw ?></td>
                        <td>₹<?= number_format($balance, 2) ?></td> <!-- Display Balance -->
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- PDF Download Button -->
        <form method="post" action="download_pdf.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit">Download as PDF</button>
        </form>
    </div>
</body>
</html>

<?php
$statement->close();
$conn->close();
?>
