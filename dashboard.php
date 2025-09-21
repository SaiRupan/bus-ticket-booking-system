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

// Fetch distinct source and destination
$sourceResult = $conn->query("SELECT DISTINCT source FROM routes");
$destResult = $conn->query("SELECT DISTINCT destination FROM routes");
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
  <title>Bus Dashboard</title>
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
            <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="dashboard-container">
      <form method="POST" action="search_routes.php">
        <div class="d-flex gap-3 mb-3 align-items-center">
        <div style="flex: 1;">
            <select class="form-select" name="source" id="sourceSelect" required>
              <option value="">Leaving From</option>
              <?php while ($row = $sourceResult->fetch_assoc()) {
                  echo "<option value='{$row['source']}'>{$row['source']}</option>";
              } ?>
            </select>
          </div>
          <div>
            <span style="font-size: 24px;">⇄</span>
          </div>
          <div style="flex: 1;">
            <select class="form-select" name="destination" id="destinationSelect" required>
              <option value="">Going To</option>
              <?php while ($row = $destResult->fetch_assoc()) {
                  echo "<option value='{$row['destination']}'>{$row['destination']}</option>";
              } ?>
            </select>
          </div>
        </div>

        <div class="fare-box mb-3">
          ₹500/- Minimum Fare
        </div>

        <input type="date" name="travel_date" class="form-control mb-3" required min="<?php echo date('Y-m-d'); ?>">

        <div class="gender-box mb-3">
          <select class="form-select" name="gender" required>
            <option value="" disabled selected>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <input type="text" name="name" class="form-control mb-3" placeholder="Name" required>

        <button type="submit" class="btn btn-primary w-100">Book</button>
      </form>
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
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sourceSelect = document.getElementById('sourceSelect');
    const destinationSelect = document.getElementById('destinationSelect');

    function disableSameOption() {
      const sourceValue = sourceSelect.value.toLowerCase();

      Array.from(destinationSelect.options).forEach(option => {
        if (option.value.toLowerCase() === sourceValue && sourceValue !== "") {
          option.disabled = true;   // disable matching destination option
        } else {
          option.disabled = false;  // enable others
        }
      });
    }

    // Disable initially on page load
    disableSameOption();

    // Disable whenever source changes
    sourceSelect.addEventListener('change', disableSameOption);
  });
</script>



</body>
</html>
<?php endif; ?>
