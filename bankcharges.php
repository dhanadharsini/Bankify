<?php
require 'db.php';

$message = "";
$hideForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $charges = $_POST['charges'];
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    if ($result->num_rows == 0) {
        $message = "<div class='msg error'><strong>❌ User not found.</strong><br>Please check the ID and try again.</div>";
    } else {
        $row = $result->fetch_assoc();
        if ($amount > $row['balance']) {
            $message = "<div class='msg error'><strong>⚠️ Insufficient Balance.</strong><br>Cannot process ₹$amount. Available Balance: ₹{$row['balance']}</div>";
        } else {
            $new_balance = $row['balance'] - $amount;
            switch ($charges) {
                case "msg":
                    $sql  = "UPDATE users SET balance = $new_balance WHERE id = $id;";
                    $sql .= "INSERT INTO transact(id, Particulars, withdraw) VALUES ($id, 'msg', $amount);";
                    $conn->multi_query($sql);
                    $message = "<div class='msg success'><strong>📩 Message Charges ₹$amount deducted.</strong><br>New Balance: ₹$new_balance</div>";
                    break;

                case "neft":
                    $sql  = "UPDATE users SET balance = $new_balance WHERE id = $id;";
                    $sql .= "INSERT INTO transact(id, Particulars, withdraw) VALUES ($id, 'NEFT', $amount);";
                    $conn->multi_query($sql);
                    $message = "<div class='msg success'><strong>💸 NEFT Charges ₹$amount deducted.</strong><br>New Balance: ₹$new_balance</div>";
                    break;

                case "rtfg":
                    $sql  = "UPDATE users SET balance = $new_balance WHERE id = $id;";
                    $sql .= "INSERT INTO transact(id, Particulars, withdraw) VALUES ($id, 'rtfg', $amount);";
                    $conn->multi_query($sql);
                    $message = "<div class='msg success'><strong>🏦 RTFG Charges ₹$amount deducted.</strong><br>New Balance: ₹$new_balance</div>";
                    break;
            }
        }
    }

    $hideForm = true;
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bank Charges</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #023E8A, #0D1B2A);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 420px;
        }

        h2 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 25px;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
        }

        .msg {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 12px;
            text-align: left;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .msg.success {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-left: 6px solid #43a047;
        }

        .msg.error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 6px solid #e53935;
        }

        button {
            background-color: #1b5e20;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #388e3c;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h2 style="color: #001845">Bank Charges</h2>

        <?php if (!empty($message)) echo $message; ?>

        <form method="post" action="" <?php if ($hideForm) echo "class='hidden'"; ?>>
            <label for="id">User ID:</label>
            <input type="number" name="id" required>

            <label for="charges">Charges Type:</label>
            <select name="charges" required>
                <option value="msg">Message Charges</option>
                <option value="neft">NEFT Charges</option>
                <option value="rtfg">RTFG Charges</option>
            </select>

            <label for="amount">Amount:</label>
            <input type="number" name="amount" step="0.01" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
