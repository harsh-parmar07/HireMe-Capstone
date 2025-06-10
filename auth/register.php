<?php
include("../includes/db.php");

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);

  if ($stmt->execute()) {
    $message = "Registration successful!";
  } else {
    $message = "Error: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register | HireMe</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; }
    .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, select { width: 100%; padding: 10px; margin: 10px 0; }
    input[type=submit] { background: #28a745; color: white; border: none; cursor: pointer; }
    .message { color: green; }
  </style>
</head>
<body>
<div class="container">
  <h2>Register</h2>
  <form method="post">
    <input name="name" placeholder="Full Name" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <select name="role" required>
      <option value="">Select Role</option>
      <option value="client">Client</option>
      <option value="freelancer">Freelancer</option>
    </select>
    <input type="submit" value="Register">
  </form>
  <p class="message"><?= $message ?></p>
  <p><a href="login.php">Already registered? Login here.</a></p>
</div>
</body>
</html>
