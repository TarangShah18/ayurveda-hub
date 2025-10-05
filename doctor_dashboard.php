<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'] ?? 'Doctor';

// ✅ Fetch stats (fixed diet chart count to include all)
$pending_prakriti = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM prakriti_submissions WHERE status='pending'"))['total'];
$total_patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients"))['total'];
$total_diets = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM diet_charts"))['total']; // fixed
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE doctor_id=$doctor_id"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctor Dashboard | Ayurveda Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8fff8; font-family: 'Poppins', sans-serif; }
    .card { border-radius: 15px; }
    .navbar { background-color: #198754; }
    .btn-custom { border-radius: 10px; }
  </style>
</head>
<body>

<nav class="navbar navbar-dark px-3">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <span class="navbar-brand fw-bold">Ayurveda Hub – Doctor Dashboard</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white">Welcome, <?php echo htmlspecialchars($doctor_name); ?></span>
      <a href="logout.php?role=doctor" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h4 class="text-success mb-3">Quick Overview</h4>
  <div class="row g-3">
    <div class="col-md-3">
      <div class="card shadow text-center p-3">
        <h6>Total Patients</h6>
        <h4 class="text-success"><?php echo $total_patients; ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-center p-3">
        <h6>Available Diet Charts</h6> <!-- label clarified -->
        <h4 class="text-success"><?php echo $total_diets; ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-center p-3">
        <h6>Your Appointments</h6>
        <h4 class="text-success"><?php echo $total_appointments; ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow text-center p-3">
        <h6>Pending Prakriti Reviews</h6>
        <h4 class="text-danger"><?php echo $pending_prakriti; ?></h4>
      </div>
    </div>
  </div>

  <h4 class="text-success mt-5 mb-3">Quick Actions</h4>
  <div class="row g-3">
    <div class="col-md-4">
      <a href="create_diet.php" class="btn btn-success w-100 p-3 btn-custom shadow-sm">➕ Create Diet Chart</a>
    </div>
    <div class="col-md-4">
      <a href="diet_charts.php" class="btn btn-outline-success w-100 p-3 btn-custom shadow-sm">📋 Manage Diet Charts</a>
    </div>
    <div class="col-md-4">
      <a href="doctor_prakriti_reviews.php" class="btn btn-warning w-100 p-3 btn-custom shadow-sm text-dark">🧘 Review Prakriti Requests (<?php echo $pending_prakriti; ?>)</a>
    </div>
    <div class="col-md-4">
      <a href="patients.php" class="btn btn-outline-success w-100 p-3 btn-custom shadow-sm">👥 View Patients</a>
    </div>
    <div class="col-md-4">
      <a href="appointments.php" class="btn btn-outline-success w-100 p-3 btn-custom shadow-sm">📅 Manage Appointments</a>
    </div>
    <div class="col-md-4">
      <a href="reports.php" class="btn btn-outline-success w-100 p-3 btn-custom shadow-sm">📊 Reports & Analytics</a>
    </div>
  </div>
</div>

<footer class="text-center mt-5 py-3 text-muted small">
  Ayurveda Hub © <?php echo date("Y"); ?> | Doctor Panel
</footer>

</body>
</html>
