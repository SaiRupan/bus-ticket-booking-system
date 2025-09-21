<?php
use PHPMailer\PHPMailer\PHPMailer;

class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function makeBooking($userId, $routeId, $travelDate) {
        $sql = "INSERT INTO bookings (user_id, route_id, travel_date, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$userId, $routeId, $travelDate]);
    }

    public function getUserBookings($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendBookingEmail($to, $subject, $body) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_email@gmail.com';
            $mail->Password   = 'your_app_password';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('your_email@gmail.com', 'Bus Booking');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
