<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Banking Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Logout Button Positioned at Top Right -->
  <div class="logout-container">
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <!-- Main Dashboard -->
  <div class="dashboard">
    <h1>Banking System</h1>
    <hr>
    <h4>Welcome, <?php echo htmlspecialchars($username); ?>!</h4>

    <div class="button-group">
      <a href="transaction.html" class="btn">Basic Transaction</a>
      <a href="transfer.html" class="btn">Fund Transfer</a>
      <a href="add_user.html" class="btn">Add New User</a>
      <a href="view_users.php" class="btn">View All Users</a>
      <a href="view_statement.html" class="btn">View Bank Statement</a>
      <a href="bankcharges.html" class="btn">Bank Charges</a>
      <a href="interest.html" class="btn">Interest</a>
    </div>
  </div>

</body>
</html>