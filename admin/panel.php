<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$jobs = $conn->query("SELECT * FROM jobs ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard | HireMe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="#">HireMe Admin</a>
  <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout <i class="fas fa-sign-out-alt"></i></a>
</nav>

<div class="container mt-4">

  <!-- Users Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">User Management</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover table-bordered m-0">
        <thead class="table-light">
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= ucfirst($user['role']) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
              <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><i class="fas fa-trash-alt"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Jobs Card -->
  <div class="card shadow-sm mb-5">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">Job Listings</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover table-bordered m-0">
        <thead class="table-light">
          <tr><th>ID</th><th>Title</th><th>Budget</th><th>Client ID</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($jobs as $job): ?>
          <tr>
            <td><?= $job['id'] ?></td>
            <td><?= htmlspecialchars($job['title']) ?></td>
            <td>$<?= $job['budget'] ?></td>
            <td><?= $job['client_id'] ?></td>
            <td><?= ucfirst($job['status']) ?></td>
            <td>
              <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
              <a href="delete_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this job?')"><i class="fas fa-trash-alt"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
