<?php
require 'db.php';

$message = "";
$messageColor = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $from_id = $_POST['from_id'];
    $to_id = $_POST['to_id'];
    $amount = floatval($_POST['amount']);

    if ($from_id == $to_id) {
        $message = "⚠️ Sender and Receiver cannot be the same.";
        $messageColor = "red";
    } else {
        $sender = $conn->query("SELECT * FROM users WHERE id = $from_id")->fetch_assoc();
        $receiver = $conn->query("SELECT * FROM users WHERE id = $to_id")->fetch_assoc();

        if (!$sender || !$receiver) {
            $message = "❌ Invalid sender or receiver User ID.";
            $messageColor = "red";
        } elseif ($sender['balance'] < $amount) {
            $message = "⚠️ Insufficient balance in sender's account.";
            $messageColor = "red";
        } else {
            $conn->begin_transaction();
            try {
                $conn->query("UPDATE users SET balance = balance - $amount WHERE id = $from_id");
                $conn->query("UPDATE users SET balance = balance + $amount WHERE id = $to_id");
                $conn->query("INSERT INTO transact(id, Particulars, withdraw) VALUES ($from_id, 'Transferred to $to_id', $amount)");
                $conn->query("INSERT INTO transact(id, Particulars, deposit) VALUES ($to_id, 'Received from $from_id', $amount)");

                $conn->commit();
                $message = "✅ ₹$amount successfully transferred from User $from_id to User $to_id.";
                $messageColor = "green";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "❌ Transfer failed: " . $e->getMessage();
                $messageColor = "red";
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Transfer Status</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .container {
      max-width: 500px;
      margin: 80px auto;
      padding: 25px;
      border-radius: 10px;
      background-color: #1B263B;
      color: #E0E6ED;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .message {
      padding: 15px;
      margin-top: 20px;
      font-weight: bold;
      border-radius: 8px;
      animation: fadeIn 0.5s ease-in-out;
    }

    .green {
      background-color: #2e7d32;
      color: #fff;
    }

    .red {
      background-color: #c62828;
      color: #fff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    a.back {
      display: inline-block;
      margin-top: 25px;
      padding: 10px 20px;
      background-color: #0077B6;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }
    a.back:hover {
      background-color: #005f8a;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Transaction Status</h2>
    <?php if (!empty($message)): ?>
      <div class="message <?php echo $messageColor; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>
    <a href="index.php" class="back">🔙 Go Back to Dashboard</a>
  </div>
</body>
</html>
