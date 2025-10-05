<?php
include 'config.php';
session_start();
if(!isset($_SESSION['doctor_id'])) header("Location: ../login_doctor.php");
$did=$_SESSION['doctor_id'];

if(isset($_GET['approve'])){
    $sid=(int)$_GET['approve'];
    $conn->query("UPDATE prakriti_submissions SET status='reviewed' WHERE id=$sid");
    $conn->query("UPDATE diet_outputs SET status='approved' WHERE patient_id=(SELECT patient_id FROM prakriti_submissions WHERE id=$sid)");
}
if(isset($_GET['reject'])){
    $sid=(int)$_GET['reject'];
    $conn->query("UPDATE prakriti_submissions SET status='rejected' WHERE id=$sid");
    $conn->query("UPDATE diet_outputs SET status='rejected' WHERE patient_id=(SELECT patient_id FROM prakriti_submissions WHERE id=$sid)");
}

$pending=$conn->query("SELECT ps.*,p.name AS pname,p.email FROM prakriti_submissions ps 
JOIN patients p ON ps.patient_id=p.id WHERE ps.status='pending'");
?>
<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
  <h3 class="text-success">Pending Prakriti Submissions</h3>
  <table class="table table-bordered mt-3 bg-white">
    <tr><th>Patient</th><th>Inferred Prakriti</th><th>Scores</th><th>Action</th></tr>
    <?php while($row=$pending->fetch_assoc()): ?>
      <tr>
        <td><?=$row['pname']?> (<?=$row['email']?>)</td>
        <td><?=$row['inferred_prakriti']?></td>
        <td>V:<?=$row['total_vata_score']?> P:<?=$row['total_pitta_score']?> K:<?=$row['total_kapha_score']?></td>
        <td>
          <a href="?approve=<?=$row['id']?>" class="btn btn-success btn-sm">Approve</a>
          <a href="?reject=<?=$row['id']?>" class="btn btn-danger btn-sm">Reject</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
