<?php
session_start();

// DB connection setup
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // ✅ Check if admin login (hardcoded in code)
    if ($email === "admin@gmail.com" && $password === "admin123") {
        $_SESSION['username'] = $email;
        $_SESSION['is_admin'] = true;
        $_SESSION['user_type'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // ✅ Normal user login (from database)
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // ✅ Verify password (assuming it's hashed)
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['email'];
            $_SESSION['is_admin'] = false;
            $_SESSION['user_type'] = 'user';
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
