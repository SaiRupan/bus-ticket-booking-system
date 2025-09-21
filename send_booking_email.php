<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $busName = $_POST['bus_name'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $date = $_POST['date'];
    $price = $_POST['price'];

    // ✅ Get logged-in user's name from session
    session_start();
    $userName = $_SESSION['username'] ?? 'Guest';

    // ✅ Insert into DB
    $conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO bookings (name, bus_name, source, destination, date, price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $userName, $busName, $from, $to, $date, $price);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // ✅ Send Email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'sairupanthirunahari1@gmail.com';
        $mail->Password = 'rwqh tnfv zrzt mbfm';  // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('yourgmail@gmail.com', 'Bus Booking System');
        $mail->addAddress('sairupanthirunahari1@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'New Bus Booking Details';
        $mail->Body = "
            <h3>Bus Booking Info</h3>
            <p><strong>User:</strong> $userName</p>
            <p><strong>Bus Name:</strong> $busName</p>
            <p><strong>From:</strong> $from</p>
            <p><strong>To:</strong> $to</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Price:</strong> ₹$price</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        // echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
?>  
