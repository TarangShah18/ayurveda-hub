<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ayurveda Hub - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { 
  background-color: #f5faf2; 
  font-family: 'Poppins', sans-serif; 
  color: #2e3b2d;
}
.hero {
  text-align: center; 
  padding: 100px 20px;
}
.hero h1 {
  font-weight: 700; 
  color: #5b8c3a;
}
.hero p {
  font-size: 1.2rem;
  color: #444;
}
.btn-ayur {
  background-color: #6da34d; 
  color: white; 
  border-radius: 10px;
  padding: 12px 24px;
  font-size: 1.1rem;
  transition: 0.3s;
}
.btn-ayur:hover { background-color: #588b39; color: white; }
.features {
  background-color: #ffffff;
  border-radius: 20px;
  padding: 50px 20px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.feature-card {
  text-align: center;
  padding: 20px;
}
.feature-card h5 { color: #6da34d; margin-top: 10px; }
.feature-icon {
  font-size: 3rem; 
  color: #6da34d;
}
footer {
  margin-top: 50px; 
  text-align: center; 
  padding: 20px;
  background-color: #e8f5e1;
  color: #4a4a4a;
}
</style>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
  <h1>🌿 Welcome to Ayurveda Hub</h1>
  <p>Your personalized Ayurvedic health companion for balance, wellness, and vitality.</p>
  <a href="login.php" class="btn btn-ayur mt-4">Login / Register</a>
</section>

<!-- Features -->
<section class="features container mt-5">
  <div class="row">
    <div class="col-md-4 feature-card">
      <i class="bi bi-heart-pulse feature-icon"></i>
      <h5>Know Your Prakriti</h5>
      <p>Discover your body type and get customized lifestyle recommendations.</p>
    </div>
    <div class="col-md-4 feature-card">
      <i class="bi bi-egg-fried feature-icon"></i>
      <h5>Personalized Diet</h5>
      <p>Receive Ayurvedic diet charts crafted and approved by certified doctors.</p>
    </div>
    <div class="col-md-4 feature-card">
      <i class="bi bi-calendar-heart feature-icon"></i>
      <h5>Consult Ayurvedic Doctors</h5>
      <p>Book online appointments and get guidance for holistic healing.</p>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  Ayurveda Hub © <?php echo date("Y"); ?> | All Rights Reserved
</footer>

</body>
</html>
