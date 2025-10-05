<?php
session_start();

if (isset($_GET['role'])) {
    if ($_GET['role'] === 'doctor') {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } elseif ($_GET['role'] === 'patient') {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

// default fallback
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>
