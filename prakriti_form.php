<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login_patient.php");
    exit();
}
include("config.php");

$result = mysqli_query($conn, "SELECT * FROM prakriti_questions ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Prakriti Questionnaire</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="card shadow p-4">
    <h3 class="text-center text-success mb-4">🧘 Prakriti Assessment</h3>
    <form action="submit_prakriti.php" method="POST">
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="mb-3">
          <strong><?php echo htmlspecialchars($row['question_text']); ?></strong><br>
          <div class="form-check">
            <input type="radio" name="q<?php echo $row['id']; ?>" value="vata" required class="form-check-input">
            <label class="form-check-label"><?php echo htmlspecialchars($row['option_vata']); ?></label>
          </div>
          <div class="form-check">
            <input type="radio" name="q<?php echo $row['id']; ?>" value="pitta" class="form-check-input">
            <label class="form-check-label"><?php echo htmlspecialchars($row['option_pitta']); ?></label>
          </div>
          <div class="form-check">
            <input type="radio" name="q<?php echo $row['id']; ?>" value="kapha" class="form-check-input">
            <label class="form-check-label"><?php echo htmlspecialchars($row['option_kapha']); ?></label>
          </div>
        </div>
      <?php endwhile; ?>
      <div class="text-center">
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
