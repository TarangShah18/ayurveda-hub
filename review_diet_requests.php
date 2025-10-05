<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
  header("Location: login_doctor.php");
  exit();
}

$doctor_id = $_SESSION['doctor_id'];

$query = "
SELECT d.id AS diet_id, p.name AS patient_name, p.email, d.output, d.status, d.created_at 
FROM diet_outputs d
JOIN patients p ON d.patient_id = p.id
WHERE d.doctor_id = '$doctor_id' AND d.status = 'pending'
ORDER BY d.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>Review Patient Diet Recommendations</title>
  <style>
    body { font-family: Arial; background-color:#f3f8f2; margin:30px; }
    table { border-collapse: collapse; width:100%; background:#fff; }
    th, td { border:1px solid #ccc; padding:10px; text-align:left; }
    th { background:#e0f3e0; }
    .approve-btn { background:#4caf50; color:#fff; padding:6px 12px; border:none; cursor:pointer; }
    .reject-btn { background:#f44336; color:#fff; padding:6px 12px; border:none; cursor:pointer; }
  </style>
</head>
<body>
  <h2>Review Patient Diet Recommendations</h2>

  <?php if(mysqli_num_rows($result) == 0): ?>
      <p>No pending recommendations right now.</p>
  <?php else: ?>
      <table>
        <tr>
          <th>Patient</th>
          <th>Email</th>
          <th>Suggested Plan</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $row['patient_name'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['output'] ?></td>
          <td><?= ucfirst($row['status']) ?></td>
          <td>
            <form action='update_diet_status.php' method='POST' style='display:inline-block'>
              <input type='hidden' name='diet_id' value='<?= $row['diet_id'] ?>'>
              <button type='submit' name='action' value='approve' class='approve-btn'>Approve</button>
            </form>
            <form action='update_diet_status.php' method='POST' style='display:inline-block'>
              <input type='hidden' name='diet_id' value='<?= $row['diet_id'] ?>'>
              <button type='submit' name='action' value='reject' class='reject-btn'>Reject</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
  <?php endif; ?>
</body>
</html>
