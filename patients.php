<?php
session_start();
include("config.php");
if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}
$patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patients List</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body { background-color: #f4f8f2; font-family: 'Poppins', sans-serif; }
.table th { background-color: #8cc63f; color: white; }
.card { border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
</style>
</head>
<body class="p-4">
<div class="container">
  <h2 class="text-success mb-4">👥 Registered Patients</h2>
  <div class="card p-3">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Gender</th>
          <th>Age</th>
          <th>Prakriti</th>
          <th>Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p = mysqli_fetch_assoc($patients)): ?>
        <tr>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo htmlspecialchars($p['email']); ?></td>
          <td><?php echo htmlspecialchars($p['gender']); ?></td>
          <td><?php echo htmlspecialchars($p['age']); ?></td>
          <td><?php echo $p['prakriti'] ?: 'Pending'; ?></td>
          <td><?php echo date("d M Y", strtotime($p['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
