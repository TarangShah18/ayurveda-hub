<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch all diet charts (not just the current doctor's)
$result = mysqli_query($conn, "
    SELECT dc.*, d.name AS doctor_name 
    FROM diet_charts dc
    LEFT JOIN doctors d ON dc.created_by = d.id
    ORDER BY dc.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Diet Charts - Ayurveda Hub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8fff8; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.card-header { background-color: #198754; color: white; font-weight: 600; }
.btn-ayur { background-color: #6da34d; color: white; border-radius: 10px; }
.btn-ayur:hover { background-color: #588b39; color: white; }
.table thead { background-color: #d7f0d3; }
</style>
</head>
<body class="p-4">
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-success">🥗 Available Diet Charts</h2>
    <div>
      <a href="create_diet.php" class="btn btn-ayur me-2">➕ Create New Chart</a>
      <a href="doctor_dashboard.php" class="btn btn-outline-success">⬅ Back to Dashboard</a>
    </div>
  </div>

  <!-- Diet Chart List -->
  <div class="card">
    <div class="card-header">📋 All Diet Plans</div>
    <div class="card-body">

      <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info text-center">
          No diet charts found in the system.
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="text-center">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Prakriti Type</th>
                <th>Description</th>
                <th>Meals</th>
                <th>Calories</th>
                <th>Weeks</th>
                <th>Created By</th>
                <th>Created On</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($row['prakriti_type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                  <strong>Morning:</strong> <?php echo htmlspecialchars($row['morning_meal']); ?><br>
                  <strong>Afternoon:</strong> <?php echo htmlspecialchars($row['afternoon_meal']); ?><br>
                  <strong>Evening:</strong> <?php echo htmlspecialchars($row['evening_meal']); ?><br>
                  <strong>Snacks:</strong> <?php echo htmlspecialchars($row['snacks']); ?>
                </td>
                <td class="text-center"><?php echo htmlspecialchars($row['daily_calories']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($row['duration_weeks']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($row['doctor_name'] ?? '—'); ?></td>
                <td class="text-center"><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>
