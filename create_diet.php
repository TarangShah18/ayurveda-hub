<?php
session_start();
include("config.php");
if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}
$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $prakriti_type = $_POST['prakriti_type'];
    $description = $_POST['description'];
    $morning_meal = $_POST['morning_meal'];
    $afternoon_meal = $_POST['afternoon_meal'];
    $evening_meal = $_POST['evening_meal'];
    $snacks = $_POST['snacks'];
    $daily_calories = $_POST['daily_calories'];
    $duration_weeks = $_POST['duration_weeks'];

    $stmt = $conn->prepare("INSERT INTO diet_charts 
        (name, prakriti_type, description, morning_meal, afternoon_meal, evening_meal, snacks, daily_calories, duration_weeks, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdii", $name, $prakriti_type, $description, $morning_meal, $afternoon_meal, $evening_meal, $snacks, $daily_calories, $duration_weeks, $doctor_id);
    
    if ($stmt->execute()) {
        $msg = "Diet chart created successfully!";
    } else {
        $err = "Error creating diet chart!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Diet Chart</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body { background-color: #f4f8f2; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.btn-ayur { background-color: #6da34d; color: white; border-radius: 10px; }
.btn-ayur:hover { background-color: #588b39; color: white; }
</style>
</head>
<body class="p-4">
<div class="container">
  <h2 class="mb-4 text-success">🥗 Create New Diet Chart</h2>

  <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
  <?php if(isset($err)): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

  <form method="POST" class="card p-4">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Diet Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6 mb-3">
        <label>Prakriti Type</label>
        <select name="prakriti_type" class="form-select" required>
          <option value="Vata">Vata</option>
          <option value="Pitta">Pitta</option>
          <option value="Kapha">Kapha</option>
          <option value="All">All</option>
        </select>
      </div>
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" rows="3" required></textarea>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Morning Meal</label>
        <textarea name="morning_meal" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-md-6 mb-3">
        <label>Afternoon Meal</label>
        <textarea name="afternoon_meal" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-md-6 mb-3">
        <label>Evening Meal</label>
        <textarea name="evening_meal" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-md-6 mb-3">
        <label>Snacks</label>
        <textarea name="snacks" class="form-control" rows="2"></textarea>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Daily Calories</label>
        <input type="number" name="daily_calories" class="form-control" required>
      </div>
      <div class="col-md-6 mb-3">
        <label>Duration (weeks)</label>
        <input type="number" name="duration_weeks" class="form-control" required>
      </div>
    </div>
    <button type="submit" class="btn btn-ayur mt-3">Create Diet</button>
    <a href="doctor_dashboard.php" class="btn btn-outline-secondary mt-3">Back</a>
  </form>
</div>
</body>
</html>
