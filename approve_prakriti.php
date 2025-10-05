<?php
session_start();
include("config.php");

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login_doctor.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$submission_id = $_POST['submission_id'];
$prakriti = $_POST['prakriti'];
$action = $_POST['action'];
$notes = mysqli_real_escape_string($conn, $_POST['doctor_notes'] ?? '');

if ($action === 'approve') {
    // Fetch submission info
    $sub = mysqli_fetch_assoc(mysqli_query($conn, "SELECT patient_id FROM prakriti_submissions WHERE id=$submission_id"));
    $patient_id = $sub['patient_id'];

    // Find diet chart for prakriti
    $chart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM diet_charts WHERE prakriti_type='$prakriti' LIMIT 1"));
    $chart_id = $chart ? $chart['id'] : 'NULL';

    // Approve and create output record
    mysqli_query($conn, "UPDATE prakriti_submissions 
                         SET status='reviewed', doctor_notes='$notes' 
                         WHERE id=$submission_id");

    mysqli_query($conn, "INSERT INTO diet_outputs (patient_id, doctor_id, chart_id, output, status, valid_until)
                         VALUES ($patient_id, $doctor_id, $chart_id, 'Approved diet chart for $prakriti prakriti', 'approved', DATE_ADD(CURDATE(), INTERVAL 30 DAY))");

    echo "<script>alert('Submission approved and diet chart generated.');window.location='doctor_prakriti_reviews.php';</script>";
} 
else {
    mysqli_query($conn, "UPDATE prakriti_submissions SET status='rejected', doctor_notes='$notes' WHERE id=$submission_id");
    echo "<script>alert('Submission rejected.');window.location='doctor_prakriti_reviews.php';</script>";
}
?>
