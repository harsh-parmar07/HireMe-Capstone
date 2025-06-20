<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
  header("Location: ../auth/login.php");
  exit;
}

$freelancer_id = $_SESSION['user_id'];

// Get all accepted jobs for this freelancer
$my_jobs = $conn->query("
  SELECT j.*, p.status AS proposal_status, p.id AS proposal_id, p.job_id AS job_id, c.name AS client_name
  FROM proposals p
  JOIN jobs j ON p.job_id = j.id
  JOIN users c ON j.client_id = c.id
  WHERE p.freelancer_id = $freelancer_id AND p.status = 'accepted'
  ORDER BY j.status DESC, j.id DESC
")->fetch_all(MYSQLI_ASSOC);

// Fetch ratings
$ratings = [];
$ratings_result = $conn->query("SELECT * FROM ratings WHERE freelancer_id = $freelancer_id");
while ($r = $ratings_result->fetch_assoc()) {
  $ratings[$r['job_id']] = $r;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Freelancer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand text-white">HireMe Freelancer</span>
  <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">
  <h3>My Jobs</h3>
  <?php if (empty($my_jobs)): ?>
    <p>You have no accepted jobs yet.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Job Title</th>
          <th>Client</th>
          <th>Status</th>
          <th>Message</th>
          <th>Rating</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($my_jobs as $job): ?>
          <tr>
            <td><?= htmlspecialchars($job['title']) ?></td>
            <td><?= htmlspecialchars($job['client_name']) ?></td>
            <td><?= ucfirst($job['status']) ?></td>
            <td>
              <a href="messages.php?job_id=<?= $job['id'] ?>&client_id=<?= $job['client_id'] ?>" class="btn btn-sm btn-primary">Message</a>
            </td>
            <td>
              <?php if (isset($ratings[$job['id']])): ?>
                <?= $ratings[$job['id']]['stars'] ?>★<br>
                <small><?= htmlspecialchars($ratings[$job['id']]['comment']) ?></small>
              <?php else: ?>
                <span class="text-muted">No rating</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
