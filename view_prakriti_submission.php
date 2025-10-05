<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login_doctor.php");
    exit();
}

$id = $_GET['id'];
$doctor_id = $_SESSION['doctor_id'];

// Fetch submission and answers
$query = "SELECT ps.*, p.name AS patient_name 
          FROM prakriti_submissions ps
          JOIN patients p ON ps.patient_id = p.id
          WHERE ps.id = $id";
$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);
$answers = json_decode($data['answers'], true);

// Fetch all questions
$q_query = mysqli_query($conn, "SELECT * FROM prakriti_questions");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Submission</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-success">
  <div class="container-fluid text-white">
    <h5 class="mb-0">Doctor Review – <?php echo htmlspecialchars($data['patient_name']); ?></h5>
    <a href="doctor_prakriti_reviews.php" class="btn btn-light btn-sm">Back</a>
  </div>
</nav>

<div class="container mt-4">
  <div class="card shadow-lg p-4">
    <h5 class="text-success mb-3">Inferred Prakriti: <?php echo $data['inferred_prakriti']; ?></h5>

    <form action="approve_prakriti.php" method="POST">
      <input type="hidden" name="submission_id" value="<?php echo $id; ?>">
      <input type="hidden" name="prakriti" value="<?php echo $data['inferred_prakriti']; ?>">

      <?php while($q = mysqli_fetch_assoc($q_query)): ?>
        <div class="mb-3">
          <p><strong><?php echo $q['question_text']; ?></strong></p>
          <p>Answer: 
            <?php
              $ans = isset($answers[$q['id']]) ? ucfirst($answers[$q['id']]) : 'Not Answered';
              echo "<span class='badge bg-info text-dark'>$ans</span>";
            ?>
          </p>
        </div>
      <?php endwhile; ?>

      <div class="mb-3">
        <label class="form-label">Doctor Notes (Optional):</label>
        <textarea class="form-control" name="doctor_notes" rows="3"></textarea>
      </div>

      <button type="submit" name="action" value="approve" class="btn btn-success w-100 mb-2">Approve & Generate Diet Chart</button>
      <button type="submit" name="action" value="reject" class="btn btn-danger w-100">Reject</button>
    </form>
  </div>
</div>
</body>
</html>
