<?php
require 'db.php';

$message = "";
$messageColor = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $balance = floatval($_POST['balance']);

    // Check if user already exists
    $check = $conn->query("SELECT * FROM users WHERE id = $id");
    if ($check->num_rows > 0) {
        $message = "⚠️ User ID already exists. Please use a different ID.";
        $messageColor = "red";
    } else {
        $sql = "INSERT INTO users (id, name, balance, opening) VALUES ($id, '$name', $balance, $balance)";
        if ($conn->query($sql)) {
            $message = "✅ User added successfully.";
            $messageColor = "green";
        } else {
            $message = "❌ Error: " . $conn->error;
            $messageColor = "red";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .message {
      margin-top: 15px;
      padding: 12px;
      font-weight: bold;
      border-radius: 6px;
      text-align: center;
      animation: fadeIn 0.4s ease-in-out;
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
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .form-box {
      max-width: 400px;
      margin: 60px auto;
      padding: 25px;
      border-radius: 8px;
      background-color: #1B263B;
      color: #E0E6ED;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    input, button {
      margin: 10px 0;
      padding: 10px;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Add User</h2>
    <form method="POST">
      <label>User ID:</label>
      <input type="number" name="id" required>
      <label>Name:</label>
      <input type="text" name="name" required>
      <label>Opening Balance:</label>
      <input type="number" step="0.01" name="balance" required>
      <button type="submit">Add User</button>
    </form>

    <?php if (!empty($message)) : ?>
      <div class="message <?php echo $messageColor; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
