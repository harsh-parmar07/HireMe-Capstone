<?php
include("../includes/db.php");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $budget = $_POST['budget'];
  $status = $_POST['status'];
  $stmt = $conn->prepare("UPDATE jobs SET title=?, budget=?, status=? WHERE id=?");
  $stmt->bind_param("sdsi", $title, $budget, $status, $id);
  $stmt->execute();
  header("Location: panel.php");
}

$job = $conn->query("SELECT * FROM jobs WHERE id=$id")->fetch_assoc();
?>

<form method="post" class="container mt-4">
  <h3>Edit Job</h3>
  <input name="title" class="form-control mb-2" value="<?= htmlspecialchars($job['title']) ?>" required>
  <input name="budget" type="number" class="form-control mb-2" value="<?= $job['budget'] ?>" required>
  <select name="status" class="form-control mb-2">
    <option value="open" <?= $job['status']=='open'?'selected':'' ?>>Open</option>
    <option value="in progress" <?= $job['status']=='in progress'?'selected':'' ?>>In Progress</option>
    <option value="completed" <?= $job['status']=='completed'?'selected':'' ?>>Completed</option>
  </select>
  <button class="btn btn-success">Update</button>
</form>
