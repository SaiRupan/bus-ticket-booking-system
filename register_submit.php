<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password_raw = $_POST['password'] ?? '';

    // Basic server-side validation
    if (empty($fullname) || empty($phone) || empty($email) || empty($password_raw)) {
        echo "<script>alert('All fields are required'); window.history.back();</script>";
        exit;
    }

    // Server-side phone number format validation
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
        echo "<script>alert('Phone number must be 10 digits and start with 6,7,8, or 9'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    // ✅ Connect to MySQL
    $conn = new mysqli('localhost', 'root', 'leooffice', 'bus_booking');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            $conn->close();
            $email_error = urlencode('Email already registered. Please use a different email.');
            $fullname_val = urlencode($fullname);
            $phone_val = urlencode($phone);
            header("Location: register.php?email_error=$email_error&fullname=$fullname_val&phone=$phone_val&email=" . urlencode($email));
            exit;
        }
        $checkStmt->close();
    } else {
        echo "<script>alert('Prepare failed: " . $conn->error . "'); window.history.back();</script>";
        $conn->close();
        exit;
    }

    // Check if phone already exists
    $checkPhoneStmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    if ($checkPhoneStmt) {
        $checkPhoneStmt->bind_param("s", $phone);
        $checkPhoneStmt->execute();
        $checkPhoneStmt->store_result();
        if ($checkPhoneStmt->num_rows > 0) {
            $checkPhoneStmt->close();
            $conn->close();
            $phone_error = urlencode('Phone number already registered. Please use a different phone number.');
            $fullname_val = urlencode($fullname);
            $email_val = urlencode($email);
            header("Location: register.php?phone_error=$phone_error&fullname=$fullname_val&phone=" . urlencode($phone) . "&email=$email_val");
            exit;
        }
        $checkPhoneStmt->close();
    } else {
        echo "<script>alert('Prepare failed: " . $conn->error . "'); window.history.back();</script>";
        $conn->close();
        exit;
    }

    // ✅ Insert into database
    $stmt = $conn->prepare("INSERT INTO users (fullname, phone, email, password) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $fullname, $phone, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registered successfully, go back to login page'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Prepare failed: " . $conn->error . "'); window.history.back();</script>";
    }

    $conn->close();
}
?>
