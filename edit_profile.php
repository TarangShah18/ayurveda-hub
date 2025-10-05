<?php
session_start();
include("config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Fetch existing details
$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE id='$patient_id'"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $bmi = $_POST['bmi'];
    $water_intake = $_POST['water_intake'];
    $sleep_hours = $_POST['sleep_hours'];
    $address = $_POST['address'];

    $update = $conn->prepare("UPDATE patients SET name=?, phone=?, age=?, gender=?, weight=?, bmi=?, water_intake=?, sleep_hours=?, address=? WHERE id=?");
    $update->bind_param("ssisdidssi", $name, $phone, $age, $gender, $weight, $bmi, $water_intake, $sleep_hours, $address, $patient_id);

    if ($update->execute()) {
        $msg = "Profile updated successfully!";
        $patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE id='$patient_id'"));
    } else {
        $err = "Error updating profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile - Ayurveda Hub</title>
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
  <h2 class="text-success mb-4">👤 Edit Profile</h2>
  
  <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
  <?php if(isset($err)): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

  <div class="card p-4">
    <form method="POST">
      <div class="row mb-3">
        <div class="col-md-6">
          <label>Name</label>
          <input type="text" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Phone</label>
          <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" class="form-control">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-3">
          <label>Age</label>
          <input type="number" name="age" value="<?php echo htmlspecialchars($patient['age']); ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <label>Gender</label>
          <select name="gender" class="form-select">
            <option value="Male" <?php if($patient['gender']=='Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if($patient['gender']=='Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if($patient['gender']=='Other') echo 'selected'; ?>>Other</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Weight (kg)</label>
          <input type="number" step="0.1" name="weight" value="<?php echo htmlspecialchars($patient['weight']); ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <label>BMI</label>
          <input type="number" step="0.1" name="bmi" value="<?php echo htmlspecialchars($patient['bmi']); ?>" class="form-control">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label>Water Intake (glasses/day)</label>
          <input type="number" name="water_intake" value="<?php echo htmlspecialchars($patient['water_intake']); ?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label>Sleep Hours</label>
          <input type="number" step="0.1" name="sleep_hours" value="<?php echo htmlspecialchars($patient['sleep_hours']); ?>" class="form-control">
        </div>
      </div>

      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
      </div>

      <button type="submit" class="btn btn-ayur mt-3">Save Changes</button>
      <a href="patient_dashboard.php" class="btn btn-outline-secondary mt-3">Back to Dashboard</a>
    </form>
  </div>
</div>
</body>
</html>
