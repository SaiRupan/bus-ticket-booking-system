<?php
class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBookings() {
        $stmt = $this->conn->query("SELECT * FROM bookings");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveBooking($bookingId) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = 'Confirmed' WHERE id = ?");
        return $stmt->execute([$bookingId]);
    }

    public function rejectBooking($bookingId) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = 'Rejected' WHERE id = ?");
        return $stmt->execute([$bookingId]);
    }
}
