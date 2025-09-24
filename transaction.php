<?php
require 'db.php';

$message = ""; 
$hideForm = false; // Flag to hide form after submission

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    $result = $conn->query("SELECT * FROM users WHERE id = $id");

    if ($result->num_rows == 0) {
        $message = "<div class='msg error'>User not found.</div>";
    } else {
        $row = $result->fetch_assoc();

        switch ($action) {
            case "view":
                $message = "<div class='msg info'>User ID: $id<br>Balance: ₹" . $row['balance'] . "</div>";
                break;

            case "deposit":
                $new_balance = $row['balance'] + $amount;
                $sql  = "UPDATE users SET balance = $new_balance WHERE id = $id;";
                $sql .= "INSERT INTO transact(id, Particulars, deposit) VALUES ($id, 'deposit', $amount);";
                $conn->multi_query($sql);
                $message = "<div class='msg success'>₹$amount deposited successfully. New Balance: ₹$new_balance</div>";
                break;

            case "withdraw":
                if ($amount > $row['balance']) {
                    $message = "<div class='msg error'>Insufficient balance.</div>";
                } else {
                    $new_balance = $row['balance'] - $amount;
                    $sql  = "UPDATE users SET balance = $new_balance WHERE id = $id;";
                    $sql .= "INSERT INTO transact(id, Particulars, withdraw) VALUES ($id, 'withdraw', $amount);";
                    $conn->multi_query($sql);
                    $message = "<div class='msg success'>₹$amount withdrawn successfully. New Balance: ₹$new_balance</div>";
                }
                break;
        }
    }

    $hideForm = true; // Set flag to hide form
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Page</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e3f2fd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #0d47a1;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .msg {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }

        .msg.success {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .msg.error {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .msg.info {
            background-color: #bbdefb;
            color: #0d47a1;
        }

        button {
            background-color: #0d47a1;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1565c0;
        }

        #amount-group {
            display: none;
        }

        .hidden {
            display: none;
        }

        #username-label {
            font-weight: bold;
            color: #0d47a1;
            margin-top: 5px;
            display: block;
        }
    </style>

    <script>
        function toggleAmountField() {
            var action = document.getElementById("action").value;
            var amountGroup = document.getElementById("amount-group");
            if (action === "deposit" || action === "withdraw") {
                amountGroup.style.display = "block";
            } else {
                amountGroup.style.display = "none";
            }
        }

        window.onload = function() {
            toggleAmountField();

            document.getElementById('id').addEventListener('input', function() {
                let userId = this.value;
                let usernameLabel = document.getElementById('username-label');

                if (userId.length === 0) {
                    usernameLabel.textContent = "";
                    return;
                }

                fetch(`get_username.php?id=${userId}`)
                    .then(response => response.text())
                    .then(name => {
                        if (name) {
                            usernameLabel.textContent = "Username: " + name;
                        } else {
                            usernameLabel.textContent = "User not found";
                        }
                    })
                    .catch(() => {
                        usernameLabel.textContent = "Error fetching username";
                    });
            });
        };
    </script>
</head>
<body>
    <div class="container">
        <h2>Bank Transaction</h2>

        <?php if (!empty($message)) echo $message; ?>

        <form method="post" action="" <?php if ($hideForm) echo "class='hidden'"; ?>>
            <label for="id">User ID:</label>
            <input type="number" name="id" id="id" required>

            <label id="username-label"></label>

            <label for="action">Action:</label>
            <select name="action" id="action" onchange="toggleAmountField()" required>
                <option value="view">View Balance</option>
                <option value="deposit">Deposit</option>
                <option value="withdraw">Withdraw</option>
            </select>

            <div id="amount-group">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" step="0.01">
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
