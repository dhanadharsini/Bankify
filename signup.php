<?php
require 'db.php';

$signupMsg = "";     // Message text
$signupColor = "";   // Message color

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users_login WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $signupMsg = "⚠️ Username already exists. Please choose another.";
        $signupColor = "red";
    } else {
        $stmt = $conn->prepare("INSERT INTO users_login (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $signupMsg = "✅ Signup successful! <a href='login.php' style='color: #fff; text-decoration: underline;'>Login here</a>";
            $signupColor = "green";
        } else {
            $signupMsg = "❌ Error: " . $conn->error;
            $signupColor = "red";
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Signup</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .message {
      margin-top: 15px;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
      display: inline-block;
      width: 100%;
      animation: fadeIn 0.5s ease-in-out;
    }
    .message.green {
      background-color: #2e7d32;
      color: white;
    }
    .message.red {
      background-color: #c62828;
      color: white;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Signup</h2>
    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" required>
      <label>Password:</label>
      <input type="password" name="password" required>
      <button type="submit">Sign Up</button>
    </form>

    <?php if (!empty($signupMsg)) : ?>
      <div class="message <?php echo $signupColor; ?>">
        <?php echo $signupMsg; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
