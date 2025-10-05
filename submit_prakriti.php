<?php
session_start();
include("config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: ../login_patient.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Initialize scores
$vata = $pitta = $kapha = 0;
$answers = [];

// Fetch questions
$questions = mysqli_query($conn, "SELECT * FROM prakriti_questions");

while ($q = mysqli_fetch_assoc($questions)) {
    $qid = $q['id'];
    if (!isset($_POST["q$qid"])) continue;

    $ans = $_POST["q$qid"];
    $answers["q$qid"] = $ans;

    // Weight calculation based on schema
    if ($ans == 'vata') $vata += $q['weight_vata'];
    if ($ans == 'pitta') $pitta += $q['weight_pitta'];
    if ($ans == 'kapha') $kapha += $q['weight_kapha'];
}

// Determine prakriti
$prakriti_type = 'Vata';
if ($pitta > $vata && $pitta > $kapha) $prakriti_type = 'Pitta';
elseif ($kapha > $vata && $kapha > $pitta) $prakriti_type = 'Kapha';

// Insert submission (status pending)
$stmt = $conn->prepare("
    INSERT INTO prakriti_submissions 
    (patient_id, answers, inferred_prakriti, total_vata_score, total_pitta_score, total_kapha_score, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
");
$json_answers = json_encode($answers);
$stmt->bind_param("issiii", $patient_id, $json_answers, $prakriti_type, $vata, $pitta, $kapha);
$stmt->execute();

// Fetch chart template for this prakriti
$chart = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT * FROM diet_charts WHERE prakriti_type='$prakriti_type' LIMIT 1"
));

// Insert diet suggestion (pending)
if ($chart) {
    $desc = "Suggested $prakriti_type-balancing diet (Pending Doctor Review)";
    $stmt2 = $conn->prepare("
        INSERT INTO diet_outputs 
        (patient_id, chart_id, output, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ");
    $output_text = "Diet chart recommendation: " . $desc;
    $stmt2->bind_param("iis", $patient_id, $chart['id'], $output_text);
    $stmt2->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Prakriti Submitted</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container my-5">
  <div class="card shadow p-5 text-center">
    <h2 class="text-success">✅ Prakriti Questionnaire Submitted</h2>
    <p>Your dominant Prakriti type is <strong><?php echo htmlspecialchars($prakriti_type); ?></strong>.</p>
    <p>Your doctor will review and approve your personalized diet plan shortly.</p>
    <a href="patient_dashboard.php" class="btn btn-success mt-3">Back to Dashboard</a>
  </div>
</div>
</body>
</html>
