<?php
include '../config.php';
session_start();
if(!isset($_SESSION['patient_id'])) header("Location: ../login_patient.php");
$pid=$_SESSION['patient_id'];
$q=$conn->query("SELECT d.*,c.name as chart_name,c.prakriti_type FROM diet_outputs d 
LEFT JOIN diet_charts c ON d.chart_id=c.id 
WHERE d.patient_id=$pid AND d.status='approved'");
?>
<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
  <h3 class="text-success mb-3">My Approved Diet Charts</h3>
  <?php if($q->num_rows==0) echo "<p>No approved charts yet.</p>"; ?>
  <?php while($row=$q->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5><?=$row['chart_name']?> (<?=$row['prakriti_type']?>)</h5>
        <p><?=$row['output']?></p>
        <span class="badge bg-success">Approved</span>
      </div>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>
