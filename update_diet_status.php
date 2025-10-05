<?php
include("config.php");
session_start();

if (!isset($_SESSION['doctor_id'])) {
  header("Location: login_doctor.php");
  exit();
}

if (isset($_POST['diet_id']) && isset($_POST['action'])) {
  $diet_id = $_POST['diet_id'];
  $action = $_POST['action'];

  if ($action == 'approve') {
    $status = 'approved';
  } elseif ($action == 'reject') {
    $status = 'rejected';
  } else {
    $status = 'pending';
  }

  $query = "UPDATE diet_outputs SET status='$status' WHERE id='$diet_id'";
  mysqli_query($conn, $query);

  header("Location: review_diet_requests.php");
  exit();
}
?>
