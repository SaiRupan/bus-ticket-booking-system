<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: routes.php");
    exit();
}

$route_id = intval($_GET['id']);

// DB connection
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$delete_sql = "DELETE FROM routes WHERE id = $route_id";
if ($conn->query($delete_sql) === TRUE) {
    header("Location: routes.php?msg=Route+deleted+successfully");
    exit();
} else {
    header("Location: routes.php?error=Failed+to+delete+route");
    exit();
}
?>
