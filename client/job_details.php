<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$job_id = $_GET['id'];
$client_id = $_SESSION['user_id'];

if (isset($_GET['action']) && isset($_GET['bid_id'])) {
  $action = $_GET['action'];
  $bid_id = $_GET['bid_id'];
  if (in_array($action, ['accept', 'reject'])) {
    $status = $action === 'accept' ? 'accepted' : 'rejected';
    $conn->query("UPDATE proposals SET status='$status' WHERE id=$bid_id AND job_id=$job_id");
  }
}

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $job_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Job not found or unauthorized.";
  exit;
}

$job = $result->fetch_assoc();
$bids = $conn->query("SELECT p.*, u.name FROM proposals p JOIN users u ON p.freelancer_id = u.id WHERE job_id = $job_id")->fetch_all(MYSQLI_ASSOC);
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

  <h4 class="mt-4">Freelancer Bids</h4>
  <?php if (count($bids) === 0): ?>
    <p>No bids yet.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead><tr><th>Name</th><th>Bid Amount</th><th>Proposal</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($bids as $bid): ?>
          <tr>
            <td><?= htmlspecialchars($bid['name']) ?></td>
            <td>$<?= $bid['bid_amount'] ?></td>
            <td><?= htmlspecialchars($bid['proposal_text']) ?></td>
            <td><?= ucfirst($bid['status']) ?></td>
            <td>
              <?php if ($bid['status'] === 'pending'): ?>
                <a href="?id=<?= $job_id ?>&action=accept&bid_id=<?= $bid['id'] ?>" class="btn btn-success btn-sm">Accept</a>
                <a href="?id=<?= $job_id ?>&action=reject&bid_id=<?= $bid['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
              <?php else: ?>
                <span class="text-muted">No actions</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
