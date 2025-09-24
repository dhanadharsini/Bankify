<?php
require 'db.php';

$message = "";
$hideForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    $result = $conn->query("SELECT * FROM users WHERE id = $id");

    if ($result->num_rows == 0) {
        $message = "<div class='message error'>❌ User not found.</div>";
    } else {
        $row = $result->fetch_assoc();
        $new_balance = $row['balance'] + $amount;

        $sql = "UPDATE users SET balance = $new_balance WHERE id = $id;";
        $sql .= "INSERT INTO transact(id, Particulars, deposit) VALUES ($id, 'Interest', $amount);";
        $conn->multi_query($sql);

        $message = "<div class='message success'>✅ ₹$amount interest credited successfully!<br>🧾 New Balance: ₹$new_balance</div>";
        $hideForm = true;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Interest Credit</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg,rgb(11, 62, 71),rgb(6, 15, 26));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #03045e;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #03045e;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            background-color: #0077b6;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #023e8a;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .hidden {
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Deposit Interest</h2>

        <?php if (!empty($message)) echo $message; ?>

        <form method="post" action="" <?php if ($hideForm) echo "class='hidden'"; ?>>
            <label for="id">User ID:</label>
            <input type="number" name="id" required>

            <label for="amount">Interest Amount:</label>
            <input type="number" name="amount" step="0.01" required>

            <button type="submit">💰 Submit</button>
        </form>
    </div>
</body>
</html>
