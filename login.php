<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if ($role == "patient") {
        $query = "SELECT * FROM patients WHERE email='$email'";
    } elseif ($role == "doctor") {
        $query = "SELECT * FROM doctors WHERE email='$email'";
    } else {
        $error = "Please select a valid role.";
    }

    if (!isset($error)) {
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Plain password check (no hashing)
            if ($user['password'] === $password) {
                if ($role == "patient") {
                    $_SESSION['patient_id'] = $user['id'];
                    $_SESSION['patient_name'] = $user['name'];
                    header("Location: patient_dashboard.php");
                    exit();
                } else {
                    $_SESSION['doctor_id'] = $user['id'];
                    $_SESSION['doctor_name'] = $user['name'];
                    header("Location: doctor_dashboard.php");
                    exit();
                }
            } else {
                $error = "❌ Invalid password for $role account.";
            }
        } else {
            $error = "❌ No account found with this email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Ayurveda Hub</title>
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
  width: 400px;
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
</style>
</head>
<body>

<div class="card p-4">
  <div class="text-center mb-3">
    <h3 class="text-success fw-bold">🌿 Ayurveda Hub</h3>
    <p class="text-muted">Login to your account</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
  <?php endif; ?>

  <!-- 🔹 Role Selector moved back to TOP -->
  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-semibold">Select Role</label>
      <select name="role" class="form-select" required>
        <option value="" disabled selected>-- Choose Role --</option>
        <option value="patient">Patient</option>
        <option value="doctor">Doctor</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Email Address</label>
      <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>

    <button type="submit" class="btn btn-ayur w-100 py-2 mt-2">Login</button>

    <div class="text-center mt-3">
      <p class="text-muted mb-1">New here?</p>
      <a href="register.php" class="text-success fw-semibold">Create an account</a>
    </div>
  </form>
</div>

</body>
</html>
