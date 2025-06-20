<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: ../auth/login.php");
  exit;
}

$job_id = $_GET['job_id'];
$freelancer_id = $_GET['freelancer_id'];
$client_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $message = $_POST['message'];
  $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_id, receiver_id, content) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiis", $job_id, $client_id, $freelancer_id, $message);
  $stmt->execute();
}

$messages = $conn->query("SELECT * FROM messages WHERE job_id = $job_id AND ((sender_id = $client_id AND receiver_id = $freelancer_id) OR (sender_id = $freelancer_id AND receiver_id = $client_id)) ORDER BY timestamp ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Messages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h4>Messages with Freelancer (Job #<?= $job_id ?>)</h4>
  <div class="border p-3 mb-3" style="height: 300px; overflow-y: scroll;">
    <?php foreach ($messages as $msg): ?>
      <div class="mb-2">
        <strong><?= $msg['sender_id'] == $client_id ? 'You' : 'Freelancer' ?>:</strong>
        <?= htmlspecialchars($msg['content']) ?>
        <br><small class="text-muted"><?= $msg['timestamp'] ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <form method="post" class="d-flex gap-2">
    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
    <button class="btn btn-primary">Send</button>
  </form>
  <a href="job_details.php?id=<?= $job_id ?>" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>
