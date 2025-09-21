<?php
class Route {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRoute($source, $destination, $price) {
        $stmt = $this->conn->prepare("INSERT INTO routes (source, destination, price) VALUES (?, ?, ?)");
        return $stmt->execute([$source, $destination, $price]);
    }

    public function editRoute($id, $source, $destination, $price) {
        $stmt = $this->conn->prepare("UPDATE routes SET source=?, destination=?, price=? WHERE id=?");
        return $stmt->execute([$source, $destination, $price, $id]);
    }

    public function deleteRoute($id) {
        $stmt = $this->conn->prepare("DELETE FROM routes WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getAllRoutes() {
        $stmt = $this->conn->query("SELECT * FROM routes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
