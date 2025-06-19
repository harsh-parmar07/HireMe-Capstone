<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$client_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $desc = $_POST['description'];
  $budget = $_POST['budget'];
  $deadline = $_POST['deadline'];

  $stmt = $conn->prepare("INSERT INTO jobs (client_id, title, description, budget, deadline, status) VALUES (?, ?, ?, ?, ?, 'open')");
  $stmt->bind_param("issss", $client_id, $title, $desc, $budget, $deadline);
  $stmt->execute();
  $message = "Job posted successfully!";
}

$jobs = $conn->query("SELECT * FROM jobs WHERE client_id = $client_id ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Client Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand text-white">HireMe Client</a>
  <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">
  <h3>Post a Job</h3>
  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>
  <form method="post" class="row g-2 mb-4">
    <div class="col-md-6"><input name="title" class="form-control" placeholder="Job Title" required></div>
    <div class="col-md-6"><input name="budget" class="form-control" placeholder="Budget ($)" type="number" required></div>
    <div class="col-12"><textarea name="description" class="form-control" placeholder="Job Description" required></textarea></div>
    <div class="col-md-6"><input name="deadline" class="form-control" type="date" required></div>
    <div class="col-md-6"><button class="btn btn-success w-100">Post Job</button></div>
  </form>

  <h3>My Jobs</h3>
  <table class="table table-bordered table-hover">
    <thead class="table-light"><tr><th>Title</th><th>Budget</th><th>Deadline</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
      <tr>
        <td><?= htmlspecialchars($job['title']) ?></td>
        <td>$<?= $job['budget'] ?></td>
        <td><?= $job['deadline'] ?></td>
        <td><?= ucfirst($job['status']) ?></td>
        <td><a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-primary">Details</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
