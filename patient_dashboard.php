<?php
session_start();
include("config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Fetch patient info
$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE id='$patient_id'"));

// Fetch prakriti submission
$prakriti = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM prakriti_submissions 
    WHERE patient_id='$patient_id' 
    ORDER BY created_at DESC LIMIT 1
"));

// Fetch latest diet (approved or pending)
$diet = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT d.*, c.name AS chart_name, c.prakriti_type, c.description, 
           c.morning_meal, c.afternoon_meal, c.evening_meal, c.snacks
    FROM diet_outputs d
    LEFT JOIN diet_charts c ON d.chart_id = c.id
    WHERE d.patient_id='$patient_id'
    ORDER BY d.created_at DESC LIMIT 1
"));

// Fetch appointments
$appointments = mysqli_query($conn, "
    SELECT a.*, doc.name AS doctor_name 
    FROM appointments a 
    JOIN doctors doc ON a.doctor_id = doc.id
    WHERE a.patient_id='$patient_id'
    ORDER BY a.appointment_date DESC
");

// Fetch reports
$reports = mysqli_query($conn, "SELECT * FROM reports WHERE patient_id='$patient_id'");

// ✅ If diet chart is approved, force prakriti status to "approved"
$prakriti_status = "pending";
if ($prakriti) {
    $prakriti_status = strtolower($prakriti['status'] ?? 'pending');
}
if ($diet && strtolower($diet['status']) === "approved") {
    $prakriti_status = "approved";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Dashboard - Ayurveda Hub</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body { background-color: #f4f8f2; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px; }
.card-header { background-color: #8cc63f; color: white; font-weight: 600; font-size: 1.1rem; }
.btn-ayur { background-color: #6da34d; color: white; border-radius: 10px; }
.btn-ayur:hover { background-color: #588b39; color: white; }
.status-badge { padding: 5px 10px; border-radius: 10px; color: white; font-weight: 500; }
.status-pending { background-color: #f0ad4e; }
.status-approved { background-color: #5cb85c; }
.status-rejected { background-color: #d9534f; }
</style>
</head>

<body class="p-4">
<div class="container">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>🌿 Welcome, <?php echo htmlspecialchars($patient['name']); ?>!</h2>
    <div class="d-flex gap-2">
      <a href="book_appointment.php" class="btn btn-ayur">➕ Book Appointment</a>
      <a href="logout.php?role=patient" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <!-- Profile -->
  <div class="card">
    <div class="card-header">👤 Your Profile</div>
    <div class="card-body">
      <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
      <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
      <p><strong>Age:</strong> <?php echo htmlspecialchars($patient['age']); ?></p>
      <p><strong>Prakriti Type:</strong> <?php echo $patient['prakriti'] ?: 'Not yet determined'; ?></p>
      <a href="edit_profile.php" class="btn btn-ayur btn-sm">Edit Profile</a>
    </div>
  </div>

  <!-- Prakriti -->
  <div class="card">
    <div class="card-header">🧘 Prakriti Assessment</div>
    <div class="card-body">
      <?php if(!$prakriti): ?>
        <p>You haven't taken the Prakriti test yet.</p>
        <a href="prakriti_form.php" class="btn btn-ayur">Take Prakriti Questionnaire</a>
      <?php else: ?>
        <p><strong>Status:</strong> 
          <span class="status-badge status-<?php echo htmlspecialchars($prakriti_status); ?>">
            <?php echo ucfirst($prakriti_status); ?>
          </span>
        </p>
        <p><strong>Detected Prakriti:</strong> <?php echo htmlspecialchars($prakriti['inferred_prakriti'] ?? 'Pending Review'); ?></p>
        <?php if(!empty($prakriti['doctor_notes'])): ?>
          <p><strong>Doctor Comments:</strong> <?php echo htmlspecialchars($prakriti['doctor_notes']); ?></p>
        <?php endif; ?>
        <a href="prakriti_form.php" class="btn btn-ayur btn-sm">Retake Questionnaire</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Diet Plan -->
  <div class="card">
    <div class="card-header">🥗 Your Diet Chart</div>
    <div class="card-body">
      <?php if(!$diet): ?>
        <p>No diet chart available. Please wait for doctor approval after Prakriti review.</p>
      <?php else: ?>
        <p><strong>Status:</strong> 
          <span class="status-badge status-<?php echo strtolower($diet['status']); ?>">
            <?php echo ucfirst($diet['status']); ?>
          </span>
        </p>

        <?php if(strtolower($diet['status']) === 'approved'): ?>
          <h5><?php echo htmlspecialchars($diet['chart_name'] ?? 'Suggested Diet Plan'); ?> 
            (<?php echo htmlspecialchars($diet['prakriti_type']); ?>)
          </h5>
          <p><?php echo htmlspecialchars($diet['description']); ?></p>
          <ul>
            <li><strong>Morning:</strong> <?php echo htmlspecialchars($diet['morning_meal'] ?? 'Not set'); ?></li>
            <li><strong>Afternoon:</strong> <?php echo htmlspecialchars($diet['afternoon_meal'] ?? 'Not set'); ?></li>
            <li><strong>Evening:</strong> <?php echo htmlspecialchars($diet['evening_meal'] ?? 'Not set'); ?></li>
            <li><strong>Snacks:</strong> <?php echo htmlspecialchars($diet['snacks'] ?? 'Not set'); ?></li>
          </ul>
        <?php else: ?>
          <p>Your personalized diet chart is currently <strong>under doctor review</strong>. It will appear here once approved.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Appointments -->
  <div class="card">
    <div class="card-header">📅 Appointments</div>
    <div class="card-body">
      <?php if(mysqli_num_rows($appointments) == 0): ?>
        <p class="text-muted">No appointments booked yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-success text-center">
              <tr>
                <th>Date</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php while($a = mysqli_fetch_assoc($appointments)): ?>
              <tr>
                <td><?php echo date("d M Y, h:i A", strtotime($a['appointment_date'])); ?></td>
                <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
                <td class="text-center">
                  <span class="status-badge status-<?php echo strtolower($a['status']); ?>">
                    <?php echo ucfirst($a['status']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($a['notes'] ?? '—'); ?></td>
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
