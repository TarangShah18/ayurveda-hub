<?php
// --- CONFIG.PHP ---
// Centralized database connection file for Ayurveda Hub
// Works for both local (XAMPP) and hosted (InfinityFree, 000webhost, etc.)

// Detect if running locally (XAMPP or localhost)
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Local development configuration
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "ayurveda_db";
} else {
    // Live hosting configuration (e.g., InfinityFree)
    $servername = "sqlXXX.infinityfree.com";
    $username = "YOUR_USERNAME_HERE";
    $password = "YOUR_PASSWORD_HERE";
    $dbname = "YOUR_DBNAME_HERE";

}

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("<strong style='color:red;'>Database connection failed:</strong> " . mysqli_connect_error());
}

// Optional: Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
