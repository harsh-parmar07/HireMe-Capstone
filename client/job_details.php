<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$job_id = $_GET['id'];
$client_id = $_SESSION['user_id'];

// Accept/reject bids
if (isset($_GET['action'], $_GET['bid_id'])) {
  $action = $_GET['action'];
  $bid_id = $_GET['bid_id'];
  if (in_array($action, ['accept', 'reject'])) {
    $status = $action === 'accept' ? 'accepted' : 'rejected';
    $conn->query("UPDATE proposals SET status='$status' WHERE id=$bid_id AND job_id=$job_id");
    if ($status === 'accepted') {
      $conn->query("UPDATE jobs SET status='in progress' WHERE id=$job_id");
    }
  }
}

// Mark job as completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
  $conn->query("UPDATE jobs SET status='completed' WHERE id=$job_id AND client_id=$client_id");
}

// Handle rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
  $stars = $_POST['stars'];
  $comment = $_POST['comment'];
  $freelancer_id = $_POST['freelancer_id'];
  $stmt = $conn->prepare("INSERT INTO ratings (job_id, client_id, freelancer_id, stars, comment) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("iiiis", $job_id, $client_id, $freelancer_id, $stars, $comment);
  $stmt->execute();
}

// Get job
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $job_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  echo "Job not found or unauthorized.";
  exit;
}
$job = $result->fetch_assoc();

// Get bids
$bids = $conn->query("SELECT p.*, u.name, u.id AS freelancer_id FROM proposals p JOIN users u ON p.freelancer_id = u.id WHERE job_id = $job_id")->fetch_all(MYSQLI_ASSOC);

// Check for existing rating
$checkRating = $conn->query("SELECT * FROM ratings WHERE job_id = $job_id AND client_id = $client_id");
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

  <?php if ($job['status'] === 'in progress'): ?>
    <form method="post" class="mb-4">
      <button name="mark_complete" class="btn btn-warning">Mark Job as Completed</button>
    </form>
  <?php endif; ?>

  <?php if ($job['status'] === 'completed' && $checkRating->num_rows === 0): ?>
    <h5 class="mt-4">Rate Your Freelancer</h5>
    <form method="post" class="mb-4">
      <div class="mb-2">
        <label>Stars (1–5):</label>
        <input type="number" name="stars" class="form-control" min="1" max="5" required>
      </div>
      <div class="mb-2">
        <label>Comment (optional):</label>
        <textarea name="comment" class="form-control"></textarea>
      </div>
      <input type="hidden" name="freelancer_id" value="<?= $bids[0]['freelancer_id'] ?>">
      <button name="submit_rating" class="btn btn-success">Submit Rating</button>
    </form>
  <?php endif; ?>

  <h4 class="mt-4">Freelancer Bids</h4>
  <table class="table table-bordered">
    <thead><tr><th>Name</th><th>Bid</th><th>Proposal</th><th>Status</th><th>Actions</th></tr></thead>
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
            <?php elseif ($bid['status'] === 'accepted'): ?>
              <a href="messages.php?job_id=<?= $job_id ?>&freelancer_id=<?= $bid['freelancer_id'] ?>" class="btn btn-primary btn-sm">Message</a>
            <?php else: ?>
              <span class="text-muted">No actions</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
