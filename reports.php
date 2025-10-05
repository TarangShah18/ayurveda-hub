<?php
session_start();
include("config.php");
if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}
$reports = mysqli_query($conn, "
  SELECT r.*, p.name AS patient_name 
  FROM reports r 
  LEFT JOIN patients p ON r.patient_id=p.id
  ORDER BY r.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports & Analytics</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body { background-color: #f4f8f2; font-family: 'Poppins', sans-serif; }
.table th { background-color: #8cc63f; color: white; }
.card { border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
</style>
</head>
<body class="p-4">
<div class="container">
  <h2 class="text-success mb-4">📜 Reports & Analytics</h2>
  <div class="card p-3">
    <table class="table table-bordered table-hover">
      <thead>
        <tr><th>Report Type</th><th>Patient</th><th>File</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($reports)): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['type']); ?></td>
          <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
          <td><a href="uploads/<?php echo htmlspecialchars($r['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success">View</a></td>
          <td><?php echo date("d M Y", strtotime($r['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
