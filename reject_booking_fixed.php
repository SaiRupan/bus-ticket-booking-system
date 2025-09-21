<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

if (!isset($_POST['booking_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing booking_id']);
    exit();
}

$booking_id = intval($_POST['booking_id']);

$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$stmt = $conn->prepare("UPDATE bookings SET status = 'Rejected' WHERE id = ?");
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    // Add log entry
    $logStmt = $conn->prepare("INSERT INTO logs (action, booking_id) VALUES (?, ?)");
    $action = 'Rejected';
    $logStmt->bind_param("si", $action, $booking_id);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update booking status']);
}

$stmt->close();
$conn->close();
?>
