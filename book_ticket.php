<?php
session_start();
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_SESSION['username']; // Assuming you store username in session
$bus_name = $_POST['bus_name'];
$from = $_POST['from'];
$to = $_POST['to'];
$date = $_POST['date'];
$price = $_POST['price'];

// Insert into bookings table
$stmt = $conn->prepare("INSERT INTO bookings (name, bus_name, source, destination, date, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $bus_name, $from, $to, $date, $price);
$stmt->execute();
$stmt->close();

// Include email script (this will send the email)
include 'send_booking_email.php';

header("Location: user_dashboard.php?msg=booked");
exit();
?>
