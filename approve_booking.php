<?php
session_start();

// Only admin allowed
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Process form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    $conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the status
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    // Get user name for the booking
    $stmt = $conn->prepare("SELECT user_id FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();


    // Get user email from users table by matching fullname
    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    $stmt->execute();
    $stmt->bind_result($userEmail, $userName);
    $stmt->fetch();
    $stmt->close();
    

    // Send approval email
    require 'PHPMailer.php';
    require 'Exception.php';
    require 'SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sairupanthirunahari1@gmail.com';
        $mail->Password = 'rwqh tnfv zrzt mbfm';  // App password
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sairupanthirunahari1@gmail.com', 'Bus Booking System');
        $mail->addAddress($userEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = 'Your Bus Ticket is Approved';
        $mail->Body = "
            <h3>Bus Ticket Approved</h3>
            <p>Dear {$userName},</p>
            <p>Your bus ticket booking has been approved by the admin.</p>
            <p>Thank you for choosing our service.</p>
        ";

        error_log("User email for booking ID $booking_id: " . var_export($userEmail, true));
        if (empty($userEmail)) {
            error_log("No user email found for booking ID: $booking_id");
        } else {
            error_log("Sending email to: $userEmail");
            $mail->send();
            error_log("Email sent successfully to: $userEmail");
        }
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        // Optionally uncomment the next line to display error on page for debugging
         echo "Mailer Error: " . $e->getMessage();
    }

    $conn->close();

    // Redirect back to admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}
?>
