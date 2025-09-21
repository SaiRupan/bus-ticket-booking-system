<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id <= 0) {
        header("Location: users.php");
        exit();
    }

    $conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: users.php?msg=User+deleted+successfully");
            exit();
        } else {
            $error = "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Prepare failed: " . $conn->error;
    }

    $conn->close();
    if (!empty($error)) {
        echo "<script>alert('" . addslashes($error) . "'); window.location.href='users.php';</script>";
        exit();
    }
} else {
    header("Location: users.php");
    exit();
}
?>
