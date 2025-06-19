<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
  header("Location: ../auth/login.php");
  exit;
}

$freelancer_id = $_SESSION['user_id'];
$jobs = $conn->query("SELECT * FROM jobs WHERE status = 'open' ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$bids = $conn->query("SELECT * FROM proposals WHERE freelancer_id = $freelancer_id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Freelancer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand text-white">HireMe Freelancer</a>
  <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">
  <h3>Open Jobs</h3>
  <table class="table table-hover table-bordered">
    <thead class="table-light"><tr><th>Title</th><th>Budget</th><th>Deadline</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
        <tr>
          <td><?= htmlspecialchars($job['title']) ?></td>
          <td>$<?= $job['budget'] ?></td>
          <td><?= $job['deadline'] ?></td>
          <td>
            <form method="post" action="submit_bid.php" class="d-flex gap-2">
              <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
              <input name="bid_amount" type="number" placeholder="Your Bid" class="form-control form-control-sm" required>
              <input name="proposal_text" type="text" placeholder="Proposal" class="form-control form-control-sm" required>
              <button class="btn btn-sm btn-primary">Submit</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3 class="mt-5">My Bids</h3>
  <table class="table table-bordered">
    <thead class="table-light"><tr><th>Job ID</th><th>Amount</th><th>Proposal</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($bids as $bid): ?>
        <tr>
          <td><?= $bid['job_id'] ?></td>
          <td>$<?= $bid['bid_amount'] ?></td>
          <td><?= htmlspecialchars($bid['proposal_text']) ?></td>
          <td><?= ucfirst($bid['status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
