<?php
session_start();
include("../includes/db.php");

// Redirect if not logged in or not client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$client_id = $_SESSION['user_id'];
$message = "";

// Handle job post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $desc = $_POST['description'];
  $budget = $_POST['budget'];
  $deadline = $_POST['deadline'];

  $stmt = $conn->prepare("INSERT INTO jobs (client_id, title, description, budget, deadline, status) VALUES (?, ?, ?, ?, ?, 'open')");
  $stmt->bind_param("issss", $client_id, $title, $desc, $budget, $deadline);
  if ($stmt->execute()) {
    $message = "Job posted successfully.";
  } else {
    $message = "Error posting job.";
  }
}

// Fetch client's jobs
$jobs = [];
$result = $conn->query("SELECT * FROM jobs WHERE client_id = $client_id ORDER BY id DESC");
if ($result) {
  $jobs = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Client Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Welcome, Client</h2>
  <p><a href="../auth/logout.php">Logout</a></p>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <h4>Post a Job</h4>
  <form method="post" class="mb-4">
    <input name="title" class="form-control mb-2" placeholder="Job Title" required>
    <textarea name="description" class="form-control mb-2" placeholder="Job Description" required></textarea>
    <input name="budget" type="number" class="form-control mb-2" placeholder="Budget ($)" required>
    <input name="deadline" type="date" class="form-control mb-2" required>
    <button type="submit" class="btn btn-success">Post Job</button>
  </form>

  <h4>Your Jobs</h4>
  <table class="table table-bordered">
    <thead><tr><th>Title</th><th>Budget</th><th>Deadline</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
        <tr>
          <td><?= htmlspecialchars($job['title']) ?></td>
          <td>$<?= $job['budget'] ?></td>
          <td><?= $job['deadline'] ?></td>
          <td><?= ucfirst($job['status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
