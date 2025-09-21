<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Connect to DB
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize inputs
$source = strtolower(trim(filter_input(INPUT_POST, 'source', FILTER_SANITIZE_STRING)));
$destination = strtolower(trim(filter_input(INPUT_POST, 'destination', FILTER_SANITIZE_STRING)));
$date = filter_input(INPUT_POST, 'travel_date', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);


if (!$source || !$destination || !$date || !$name || !$gender) {
    die("Invalid input. Please go back and fill all required fields.");
}

// Get matching routes
$sql = "SELECT * FROM routes WHERE LOWER(source)=? AND LOWER(destination)=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $source, $destination);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .container-content {
      margin-top: 30px; /* adjust as needed */
    }

    body {
      background: #f4faff;
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

    .card {
      border: 1px solid #007bff;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .card-body {
      background: #ffffff;
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

<div class="container content">
  
  <h2 class="text-center mt-5 pt-5  ">Available Buses for <?php echo htmlspecialchars($source); ?> ➝ <?php echo htmlspecialchars($destination); ?> on <?php echo htmlspecialchars($date); ?></h2>

  <?php
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo '<div class="card">
  <div class="card-body">
    <h5 class="card-title">Bus from ' . htmlspecialchars($row['source']) . ' to ' . htmlspecialchars($row['destination']) . '</h5>
    <p class="card-text">Fare: ₹' . htmlspecialchars($row['fare']) . '</p>
    <p class="card-text">Passenger: ' . htmlspecialchars($name) . ' (' . htmlspecialchars($gender) . ')</p>
    <p class="card-text">Date: ' . htmlspecialchars($date) . '</p>

    <form class="book-now-form" method="POST" action="send_booking_email.php" style="display:inline;">
      <input type="hidden" name="bus_name" value="Bus from ' . htmlspecialchars($row['source']) . ' to ' . htmlspecialchars($row['destination']) . '">
      <input type="hidden" name="from" value="' . htmlspecialchars($row['source']) . '">
      <input type="hidden" name="to" value="' . htmlspecialchars($row['destination']) . '">
      <input type="hidden" name="date" value="' . htmlspecialchars($date) . '">
      <input type="hidden" name="price" value="' . htmlspecialchars($row['fare']) . '">
      <button type="submit" class="btn btn-success">Book Now</button>
    </form>

    <div class="booking-message" style="margin-top:10px; color: green; font-weight: bold; display:none; text-align: center;"></div>
  </div>
</div>';
      }
  } else {
      echo '<div class="alert alert-danger">No buses found for the selected route.</div>';
  }
  ?>

  <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".book-now-form");

    forms.forEach(form => {
form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Disable the submit button to prevent multiple clicks
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        const formData = new FormData(form);
        const messageDiv = form.closest(".card-body").querySelector(".booking-message");

        // Optional loading message
        messageDiv.textContent = "Booking...";
        messageDiv.style.color = "blue";
        messageDiv.style.display = "block";

        fetch("send_booking_email.php", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(data => {
            // You can log this for debug
            console.log("Server response:", data);
            messageDiv.textContent = "Your booking is pending!";
            messageDiv.style.color = "green";
          })
          .catch(error => {
            console.error("Error:", error);
            messageDiv.textContent = "Booking failed!";
            messageDiv.style.color = "red";
            // Re-enable the button if booking failed
            submitButton.disabled = false;
          });
      });
    });
  });
</script>

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
