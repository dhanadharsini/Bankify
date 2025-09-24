<?php 
session_start();
require 'db.php';

$errorMsg = ""; // No message on first load
$showError = false; // Control visibility

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users_login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Check password
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        }
    }

    // Error if either username doesn't exist or password is incorrect
    $errorMsg = "Incorrect username / password";
    $showError = true;

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
  <style>
    #incorrect {
      color: red;
      font-size: 14px;
      margin: 8px 0;
      display: <?php echo $showError ? 'block' : 'none'; ?>;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Login</h2>
    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <!-- Error message only shows after form submission with invalid credentials -->
      <label id="incorrect"><?php echo htmlspecialchars($errorMsg); ?></label>

      <button type="submit">Login</button>
    </form>
    <br>
    <p style="text-align:center;color: green;">New user? <a href="signup.php" style="color:green;">Signup here</a></p>
  </div>
</body>
</html>
