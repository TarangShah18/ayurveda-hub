<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    if ($role === 'patient') {
        $age = (int)$_POST['age'];
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        $query = "INSERT INTO patients (name, email, password, phone, age, gender, address)
                  VALUES ('$name', '$email', '$password', '$phone', '$age', '$gender', '$address')";
    } elseif ($role === 'doctor') {
        $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);

        $query = "INSERT INTO doctors (name, email, password, specialization)
                  VALUES ('$name', '$email', '$password', '$specialization')";
    } else {
        $error = "Please select a valid role.";
    }

    if (!isset($error)) {
        if (mysqli_query($conn, $query)) {
            $success = "✅ Registration successful! You can now log in.";
        } else {
            $error = "⚠️ Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | Ayurveda Hub</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
  background: linear-gradient(135deg, #a8e063, #56ab2f);
  font-family: 'Poppins', sans-serif;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card {
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  width: 500px;
  padding: 20px;
}
.btn-ayur {
  background-color: #6da34d;
  color: white;
  border-radius: 10px;
}
.btn-ayur:hover { background-color: #588b39; color: white; }
.form-select, .form-control {
  border-radius: 10px;
  border: 1px solid #a2d39c;
}
.hidden { display: none; }
</style>
<script>
function toggleFields() {
  const role = document.getElementById('role').value;
  document.getElementById('patientFields').classList.toggle('hidden', role !== 'patient');
  document.getElementById('doctorFields').classList.toggle('hidden', role !== 'doctor');
}
</script>
</head>
<body>

<div class="card">
  <div class="text-center mb-3">
    <h3 class="text-success fw-bold">🌿 Ayurveda Hub</h3>
    <p class="text-muted">Create your account</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
  <?php elseif (!empty($success)): ?>
    <div class="alert alert-success py-2"><?php echo $success; ?></div>
  <?php endif; ?>

  <form method="POST">
    <!-- 🔹 Role Selector (on top like login) -->
    <div class="mb-3">
      <label class="form-label fw-semibold">Register As</label>
      <select name="role" id="role" class="form-select" required onchange="toggleFields()">
        <option value="" disabled selected>-- Choose Role --</option>
        <option value="patient">Patient</option>
        <option value="doctor">Doctor</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Full Name</label>
      <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Email Address</label>
      <input type="email" name="email" class="form-control" required placeholder="Enter your email">
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Password</label>
      <input type="password" name="password" class="form-control" required placeholder="Choose a password">
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Phone Number</label>
      <input type="text" name="phone" class="form-control" placeholder="+91XXXXXXXXXX">
    </div>

    <!-- 🔹 Patient-specific fields -->
    <div id="patientFields" class="hidden">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">Age</label>
          <input type="number" name="age" class="form-control" placeholder="Enter your age">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">Gender</label>
          <select name="gender" class="form-select">
            <option value="" disabled selected>-- Select --</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Address</label>
        <textarea name="address" class="form-control" rows="2" placeholder="Enter your address"></textarea>
      </div>
    </div>

    <!-- 🔹 Doctor-specific fields -->
    <div id="doctorFields" class="hidden">
      <div class="mb-3">
        <label class="form-label fw-semibold">Specialization</label>
        <input type="text" name="specialization" class="form-control" placeholder="e.g., Panchakarma, Nadi Pariksha">
      </div>
    </div>

    <button type="submit" class="btn btn-ayur w-100 py-2 mt-2">Register</button>

    <div class="text-center mt-3">
      <p class="text-muted mb-1">Already have an account?</p>
      <a href="login.php" class="text-success fw-semibold">Login here</a>
    </div>
  </form>
</div>

</body>
</html>
