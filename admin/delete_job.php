<?php
include("../includes/db.php");
$id = $_GET['id'];
$conn->query("DELETE FROM jobs WHERE id=$id");
header("Location: panel.php");
