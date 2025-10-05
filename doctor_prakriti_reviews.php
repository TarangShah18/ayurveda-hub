<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'] ?? "Doctor";

// Handle approval/rejection form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $submission_id = intval($_POST['submission_id']);
    $chart_id = intval($_POST['chart_id'] ?? 0);
    $action = $_POST['action'];
    $new_status = ($action === 'approve') ? 'reviewed' : 'rejected';
    $doctor_notes = mysqli_real_escape_string($conn, $_POST['doctor_notes']);

    // Fetch patient_id
    $res = mysqli_query($conn, "SELECT patient_id, inferred_prakriti FROM prakriti_submissions WHERE id=$submission_id LIMIT 1");
    $data = mysqli_fetch_assoc($res);
    $patient_id = $data['patient_id'] ?? 0;
    $inferred_prakriti = $data['inferred_prakriti'] ?? null;

    // 1️⃣ Update prakriti submission status
    mysqli_query($conn, "
        UPDATE prakriti_submissions 
        SET status='$new_status', doctor_notes='$doctor_notes' 
        WHERE id=$submission_id
    ");

    // 2️⃣ If approved: update diet_outputs and patient prakriti
    if ($action === 'approve' && $patient_id > 0) {
        // Update diet chart if edited
        if ($chart_id > 0) {
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $morning = mysqli_real_escape_string($conn, $_POST['morning_meal']);
            $afternoon = mysqli_real_escape_string($conn, $_POST['afternoon_meal']);
            $evening = mysqli_real_escape_string($conn, $_POST['evening_meal']);
            $snacks = mysqli_real_escape_string($conn, $_POST['snacks']);
            mysqli_query($conn, "
                UPDATE diet_charts 
                SET description='$description',
                    morning_meal='$morning',
                    afternoon_meal='$afternoon',
                    evening_meal='$evening',
                    snacks='$snacks'
                WHERE id=$chart_id
            ");
        }

        // Approve related diet output
        mysqli_query($conn, "
            UPDATE diet_outputs 
            SET doctor_id=$doctor_id, status='approved'
            WHERE patient_id=$patient_id
        ");

        // Update prakriti type in patient profile
        mysqli_query($conn, "
            UPDATE patients 
            SET prakriti='$inferred_prakriti'
            WHERE id=$patient_id
        ");
    }

    // 3️⃣ If rejected, mark linked diet as rejected
    if ($action === 'reject' && $patient_id > 0) {
        mysqli_query($conn, "
            UPDATE diet_outputs 
            SET doctor_id=$doctor_id, status='rejected'
            WHERE patient_id=$patient_id
        ");
    }

    header("Location: doctor_prakriti_reviews.php?msg=updated");
    exit();
}

// ✅ Fetch only latest pending submissions per patient (no duplicates)
$pending = mysqli_query($conn, "
    SELECT ps.*, p.name AS patient_name, p.age, p.gender, p.email, p.phone, 
           do.chart_id, dc.name AS chart_name, dc.description, 
           dc.morning_meal, dc.afternoon_meal, dc.evening_meal, dc.snacks
    FROM prakriti_submissions ps
    JOIN patients p ON ps.patient_id = p.id
    LEFT JOIN diet_outputs do ON do.patient_id = p.id AND do.status='pending'
    LEFT JOIN diet_charts dc ON do.chart_id = dc.id
    WHERE ps.status='pending'
    GROUP BY ps.patient_id
    ORDER BY ps.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor - Review Prakriti Submissions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8fff8; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); margin-bottom: 25px; }
.card-header { background-color: #198754; color: white; font-weight: 600; }
textarea { resize: vertical; }
.btn-approve { background-color: #28a745; color: #fff; border-radius: 10px; }
.btn-reject { background-color: #dc3545; color: #fff; border-radius: 10px; }
</style>
</head>
<body class="p-4">
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-success">🧘 Review Patient Prakriti & Diet Suggestions</h2>
    <a href="doctor_dashboard.php" class="btn btn-outline-success">⬅ Back to Dashboard</a>
  </div>

  <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div class="alert alert-success">✅ Review status updated successfully.</div>
  <?php endif; ?>

  <?php if (mysqli_num_rows($pending) == 0): ?>
    <div class="alert alert-info">No pending Prakriti reviews at the moment.</div>
  <?php else: ?>
    <?php while ($row = mysqli_fetch_assoc($pending)): ?>
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <span><strong>Patient:</strong> <?php echo htmlspecialchars($row['patient_name']); ?> (<?php echo htmlspecialchars($row['gender']); ?>, <?php echo htmlspecialchars($row['age']); ?> years)</span>
            <span>Email: <?php echo htmlspecialchars($row['email']); ?> | Phone: <?php echo htmlspecialchars($row['phone']); ?></span>
          </div>
        </div>
        <div class="card-body">
          <p><strong>Detected Prakriti:</strong> <?php echo $row['inferred_prakriti'] ?: 'Not yet determined'; ?></p>
          <p><strong>Scores:</strong> Vata: <?php echo $row['total_vata_score']; ?> | Pitta: <?php echo $row['total_pitta_score']; ?> | Kapha: <?php echo $row['total_kapha_score']; ?></p>

          <?php if (!empty($row['chart_id'])): ?>
          <hr>
          <h5 class="text-success mb-3">🍽 Suggested Diet Chart: <?php echo htmlspecialchars($row['chart_name']); ?></h5>
          <form method="POST">
            <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="chart_id" value="<?php echo $row['chart_id']; ?>">

            <div class="mb-3">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Morning Meal</label>
                <textarea name="morning_meal" class="form-control" rows="2"><?php echo htmlspecialchars($row['morning_meal']); ?></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Afternoon Meal</label>
                <textarea name="afternoon_meal" class="form-control" rows="2"><?php echo htmlspecialchars($row['afternoon_meal']); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Evening Meal</label>
                <textarea name="evening_meal" class="form-control" rows="2"><?php echo htmlspecialchars($row['evening_meal']); ?></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Snacks</label>
                <textarea name="snacks" class="form-control" rows="2"><?php echo htmlspecialchars($row['snacks']); ?></textarea>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Doctor Notes / Comments</label>
              <textarea name="doctor_notes" class="form-control" rows="3" placeholder="Add any notes or adjustments..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button type="submit" name="action" value="reject" class="btn btn-reject px-4">Reject</button>
              <button type="submit" name="action" value="approve" class="btn btn-approve px-4">Approve</button>
            </div>
          </form>
          <?php else: ?>
            <p class="text-muted">No diet chart suggestion found for this patient.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>

</div>
</body>
</html>
