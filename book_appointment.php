<?php
session_start();
include("config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$doctors = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];

    if (strtotime($appointment_date) < time()) {
        $err = "Appointment date must be in the future.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) VALUES (?, ?, ?, 'Pending', ?)");
        $stmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $notes);
        if ($stmt->execute()) {
            $msg = "Appointment booked successfully!";
        } else {
            $err = "Error booking appointment.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment - Ayurveda Hub</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body { background-color: #f4f8f2; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-top: 30px; }
.btn-ayur { background-color: #6da34d; color: white; border-radius: 10px; }
.btn-ayur:hover { background-color: #588b39; color: white; }
</style>
</head>

<body class="p-4">
<div class="container">
  <h2 class="text-success mb-4">📅 Book an Appointment</h2>

  <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
  <?php if(isset($err)): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

  <div class="card p-4">
    <form method="POST">
      <div class="mb-3">
        <label>Select Doctor</label>
        <select name="doctor_id" class="form-select" required>
          <option value="">-- Choose Doctor --</option>
          <?php while($d = mysqli_fetch_assoc($doctors)): ?>
            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?> (<?php echo htmlspecialchars($d['specialization']); ?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label>Appointment Date & Time</label>
        <input type="datetime-local" name="appointment_date" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Notes (optional)</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Reason for appointment, symptoms, etc."></textarea>
      </div>
      <button type="submit" class="btn btn-ayur mt-2">Book Appointment</button>
      <a href="patient_dashboard.php" class="btn btn-outline-secondary mt-2">Back to Dashboard</a>
    </form>
  </div>
</div>
</body>
</html>
