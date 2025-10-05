<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Handle status update
if (isset($_POST['update_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE appointments SET status='$new_status' WHERE id='$appointment_id' AND doctor_id='$doctor_id'");
    $_SESSION['msg'] = "Appointment status updated successfully!";
    header("Location: appointments.php");
    exit();
}

// Fetch all appointments for this doctor
$appointments = mysqli_query($conn, "
    SELECT a.*, p.name AS patient_name, p.email AS patient_email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id='$doctor_id'
    ORDER BY a.appointment_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Appointments | Doctor Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
body { background-color: #f8fff8; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px; }
.card-header { background-color: #198754; color: white; font-weight: 600; }
.btn-ayur { background-color: #198754; color: white; border-radius: 10px; }
.btn-ayur:hover { background-color: #146c43; color: white; }
.status-badge { padding: 6px 12px; border-radius: 12px; color: #fff; font-weight: 500; }
.status-pending { background-color: #f0ad4e; }
.status-approved { background-color: #5cb85c; }
.status-completed { background-color: #0275d8; }
.status-cancelled { background-color: #d9534f; }
</style>
</head>

<body class="p-4">

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-success">📅 Manage Appointments</h2>
    <a href="doctor_dashboard.php" class="btn btn-outline-success">⬅ Back to Dashboard</a>
  </div>

  <?php if(isset($_SESSION['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">Your Appointments</div>
    <div class="card-body">
      <?php if (mysqli_num_rows($appointments) == 0): ?>
        <p class="text-muted">No appointments found.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-success text-center">
              <tr>
                <th>Date & Time</th>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Notes</th>
                <th>Status</th>
                <th>Update</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = mysqli_fetch_assoc($appointments)): ?>
              <tr>
                <td><?php echo date("d M Y, h:i A", strtotime($row['appointment_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_email']); ?></td>
                <td><?php echo htmlspecialchars($row['notes'] ?? '—'); ?></td>
                <td class="text-center">
                  <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                    <?php echo ucfirst($row['status']); ?>
                  </span>
                </td>
                <td>
                  <form method="POST" class="d-flex">
                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                    <select name="status" class="form-select form-select-sm me-2">
                      <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                      <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>Approved</option>
                      <option value="completed" <?php if($row['status']=='completed') echo 'selected'; ?>>Completed</option>
                      <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-ayur btn-sm">Update</button>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
