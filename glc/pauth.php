<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Check if user is a producer
$username = mysqli_real_escape_string($con, $_SESSION["username"]);
$query = "SELECT isproducer FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // User not found
    header("Location: login.php");
    exit();
}

$row = mysqli_fetch_assoc($result);
if ($row['isproducer'] != 1) {
    // Not a producer
    header("Location: notice.php"); // or show error
    exit();
}

// Close connection
mysqli_close($con);
?>