<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $freelancer_id = $_SESSION['user_id'];
  $job_id = $_POST['job_id'];
  $amount = $_POST['bid_amount'];
  $text = $_POST['proposal_text'];

  $stmt = $conn->prepare("INSERT INTO proposals (job_id, freelancer_id, bid_amount, proposal_text, status) VALUES (?, ?, ?, ?, 'pending')");
  $stmt->bind_param("iids", $job_id, $freelancer_id, $amount, $text);
  $stmt->execute();
  header("Location: dashboard.php");
}
?>
