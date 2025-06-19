<?php
include("../includes/db.php");

session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      if ($user['role'] === 'admin') {
          header("Location: ../admin/panel.php");
      } else {
          header("Location: ../" . $user['role'] . "/dashboard.php");
      }
      exit;
    } else {
      $message = "Incorrect password.";
    }
  } else {
    $message = "User not found.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login | HireMe</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; }
    .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input { width: 100%; padding: 10px; margin: 10px 0; }
    input[type=submit] { background: #007bff; color: white; border: none; cursor: pointer; }
    .message { color: red; }
  </style>
</head>
<body>
<div class="container">
  <h2>Login</h2>
  <form method="post">
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <input type="submit" value="Login">
  </form>
  <p class="message"><?= $message ?></p>
  <p><a href="register.php">Don't have an account? Register</a></p>
</div>
</body>
</html>
