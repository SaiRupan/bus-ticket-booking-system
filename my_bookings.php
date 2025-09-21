<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header("Location: admin_dashboard.php");
    exit();
}

// Check if user is admin
$is_admin = $_SESSION['is_admin'] ?? false;

// DB connection setup
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Fetch bookings (both confirmed, pending, and rejected) for the logged-in user
$stmt = $conn->prepare("SELECT * FROM bookings WHERE name = ? AND (status = 'Confirmed' OR status = 'Pending' OR status = 'Rejected') ORDER BY date DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<?php if ($is_admin): ?>
    <div class="dashboard-container" style="max-width: 750px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);">
        <h2>Admin Dashboard</h2>
        <p>Welcome, Admin! Here you can manage the system.</p>
        <!-- Add admin controls here -->
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_routes.php">Manage Routes</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
        </ul>
    </div>
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    .wrapper {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .content {
      flex: 1;
    }

    body {
      background: url('https://images.unsplash.com/photo-1532939163844-547f958e91b4?q=80&w=1888&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    .navbar {
      background-color: #0056b3;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: 600;
    }
    .navbar-nav .nav-link:hover {
      color: #cce4ff !important;
    }
    .dashboard-container {
      margin-top: 100px;
      max-width: 800px;
      margin-inline: auto;
      background: #ffffffcc;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease;
    }
    .dashboard-container:hover {
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    form {
      display: block;
    }
    .form-select, .form-control {
      height: 55px;
      border-radius: 12px;
      border: 1px solid #ccc;
      padding: 0 15px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }
    .form-select:focus, .form-control:focus {
      border-color: #0056b3;
      outline: none;
      box-shadow: 0 0 8px rgba(0, 86, 179, 0.3);
    }
    .fare-box {
      background: #dbe9ff;
      padding: 18px;
      text-align: center;
      font-weight: 700;
      margin-bottom: 25px;
      border-radius: 12px;
      color: #0056b3;
      font-size: 18px;
      box-shadow: inset 0 0 10px rgba(0, 86, 179, 0.1);
      display: block;
      width: 100%;
    }
    .gender-box {
      display: flex;
      justify-content: center;
      gap: 15px;
      width: 100%;
    }
    .gender-box select {
      width: 100%;
      font-size: 16px;
      border-radius: 12px;
      padding: 10px 15px;
      border: 1px solid #ccc;
      transition: border-color 0.3s ease;
    }
    .gender-box select:focus {
      border-color: #0056b3;
      outline: none;
      box-shadow: 0 0 8px rgba(0, 86, 179, 0.3);
    }
    button[type="submit"] {
      background-color: #0056b3;
      color: white;
      border: none;
      border-radius: 12px;
      padding: 15px 0;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    button[type="submit"]:hover {
      background-color: #003d80;
    }
    .booking-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      background-color: #fff;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
    }
    .booking-info {
      flex: 1 1 60%;
    }
    .booking-info p {
      margin: 5px 0;
      font-size: 16px;
      color: #333;
    }
    .booking-status {
      flex: 1 1 30%;
      text-align: right;
      font-weight: bold;
      color: #4caf50;
      font-size: 18px;
    }
    footer {
      background: rgba(0, 0, 0, 0.7);
      color: white;
      text-align: center;
      padding: 20px 10px;
    }
  </style>
</head>

<body>
<div class="wrapper">
  <div class="content">
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container">
        <a class="navbar-brand" href="login.php">SRBus 🚌</a>
        <div class="collapse navbar-collapse justify-content-end">
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="dashboard-container" style="margin-top: 100px;">
      <div class="container text-center">
        <h2 style="font-weight: 700; margin-bottom: 30px;">BOOKINGS</h2>
<?php if (count($bookings) > 0): ?>
          <?php $i = 1; foreach ($bookings as $booking): ?>
            <div class="booking-card">
              <div class="booking-info">
                <p><strong>Bus Name:</strong> <?= htmlspecialchars($booking['bus_name']) ?></p>
                <p><strong>From:</strong> <?= htmlspecialchars($booking['source']) ?></p>
                <p><strong>To:</strong> <?= htmlspecialchars($booking['destination']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($booking['date']) ?></p>
                <p><strong>Price:</strong> ₹<?= htmlspecialchars($booking['price']) ?></p>
              </div>
              <div class="booking-status">
                <?php if ($booking['status'] === 'Rejected'): ?>
                  <span style="color: red; font-weight: bold;"><?= htmlspecialchars($booking['status']) ?></span>
                <?php else: ?>
                  <?= htmlspecialchars($booking['status']) ?>
                <?php endif; ?>
              </div>
            </div>
          <?php $i++; endforeach; ?>
        <?php else: ?>
          <p>No confirmed bookings found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 SR Bus. All rights reserved.
    <div class="social-icons" style="margin-top: 10px;">
      <a href="#" style="color: white; margin: 0 10px; text-decoration: none; font-size: 18px; display: inline-flex; align-items: center;">
        <i class="fab fa-instagram" style="margin-right: 5px;"></i>SR Bus
      </a>
      <a href="#" style="color: white; margin: 0 10px; text-decoration: none; font-size: 18px; display: inline-flex; align-items: center;">
        <i class="fab fa-facebook" style="margin-right: 5px;"></i>SR Bus
      </a>
    </div>
  </footer>
</div>
</body>
</html>
<?php endif; ?>
