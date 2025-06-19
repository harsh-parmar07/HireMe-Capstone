<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$job_id = $_GET['id'];
$client_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $job_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Job not found or unauthorized.";
  exit;
}

$job = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Job Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2><?= htmlspecialchars($job['title']) ?></h2>
  <p><strong>Budget:</strong> $<?= $job['budget'] ?></p>
  <p><strong>Deadline:</strong> <?= $job['deadline'] ?></p>
  <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($job['description'])) ?></p>
  <p><strong>Status:</strong> <?= ucfirst($job['status']) ?></p>
  <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
