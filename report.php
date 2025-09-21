<?php
// report.php - Generate booking report CSV for a given date

$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;
$exclude_rejected = isset($_GET['exclude_rejected']) ? $_GET['exclude_rejected'] : null;

if (!$date && (!$from_date || !$to_date)) {
    // If no date parameters provided, generate report for all bookings
    $conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT COUNT(*) AS count, SUM(price) AS total_amount FROM bookings";
    if ($exclude_rejected) {
        $sql .= " WHERE status != 'Rejected'";
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("No bookings found.");
    }

    $row = $result->fetch_assoc();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="booking_report_all.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Bookings count', 'Total Amount']);
    fputcsv($output, ['All Dates', $row['count'], $row['total_amount']]);
    fclose($output);
    $stmt->close();
    $conn->close();
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($from_date && $to_date) {
    // Prepare and execute query to get count and total amount for the date range
    $sql = "SELECT COUNT(*) AS count, SUM(price) AS total_amount FROM bookings WHERE date BETWEEN ? AND ?";
    if ($exclude_rejected) {
        $sql .= " AND status != 'Rejected'";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from_date, $to_date);
    $filename = "booking_report_{$from_date}_to_{$to_date}.csv";
} else {
    // Prepare and execute query to get count and total amount for the single date
    $sql = "SELECT date, COUNT(*) AS count, SUM(price) AS total_amount FROM bookings WHERE date = ?";
    if ($exclude_rejected) {
        $sql .= " AND status != 'Rejected'";
    }
    $sql .= " GROUP BY date";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $filename = "booking_report_{$date}.csv";
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No bookings found for the selected date(s).");
}

$row = $result->fetch_assoc();

// Prepare CSV content
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Bookings count', 'Total Amount']);

if ($from_date && $to_date) {
    fputcsv($output, ["{$from_date} to {$to_date}", $row['count'], $row['total_amount']]);
} else {
    fputcsv($output, [$row['date'], $row['count'], $row['total_amount']]);
}

fclose($output);
$stmt->close();
$conn->close();
exit;
?>
