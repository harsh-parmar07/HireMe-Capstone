<?php
include("../includes/db.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $role = $_POST['role'];
  $stmt = $conn->prepare("UPDATE users SET name=?, role=? WHERE id=?");
  $stmt->bind_param("ssi", $name, $role, $id);
  $stmt->execute();
  header("Location: panel.php");
}

$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
?>

<form method="post" class="container mt-4">
  <h3>Edit User</h3>
  <input name="name" class="form-control mb-2" value="<?= htmlspecialchars($user['name']) ?>" required>
  <select name="role" class="form-control mb-2">
    <option value="client" <?= $user['role']=='client'?'selected':'' ?>>Client</option>
    <option value="freelancer" <?= $user['role']=='freelancer'?'selected':'' ?>>Freelancer</option>
    <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
  </select>
  <button class="btn btn-success">Update</button>
</form>
