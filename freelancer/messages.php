<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
  header("Location: ../auth/login.php");
  exit;
}

$freelancer_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];
$client_id = $_GET['client_id'];

// Check that the freelancer has an accepted bid on this job
$check = $conn->query("SELECT * FROM proposals WHERE job_id = $job_id AND freelancer_id = $freelancer_id AND status = 'accepted'");
if ($check->num_rows === 0) {
  echo "Access denied: No accepted bid for this job.";
  exit;
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $message = $_POST['message'];
  $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiis", $job_id, $freelancer_id, $client_id, $message);
  $stmt->execute();
}

// Fetch messages
$messages = $conn->query("SELECT * FROM messages WHERE job_id = $job_id AND ((sender_id = $freelancer_id AND receiver_id = $client_id) OR (sender_id = $client_id AND receiver_id = $freelancer_id)) ORDER BY timestamp ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Messages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h4>Messages with Client (Job #<?= $job_id ?>)</h4>
  <div class="border p-3 mb-3" style="height: 300px; overflow-y: auto;">
    <?php foreach ($messages as $msg): ?>
      <div class="mb-2">
        <strong><?= $msg['sender_id'] == $freelancer_id ? 'You' : 'Client' ?>:</strong>
        <?= htmlspecialchars($msg['content']) ?>
        <br><small class="text-muted"><?= $msg['timestamp'] ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <form method="post" class="d-flex gap-2">
    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
    <button class="btn btn-primary">Send</button>
  </form>
  <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>
