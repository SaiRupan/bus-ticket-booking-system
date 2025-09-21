<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$source = $destination = $fare = "";
$source_err = $destination_err = $fare_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = trim($_POST["source"]);
    $destination = trim($_POST["destination"]);
    $fare = trim($_POST["fare"]);

    if (empty($source)) {
        $source_err = "Please enter source.";
    }
    if (empty($destination)) {
        $destination_err = "Please enter destination.";
    }
    if (empty($fare)) {
        $fare_err = "Please enter fare.";
    } elseif (!is_numeric($fare) || $fare < 0) {
        $fare_err = "Please enter a valid positive number for fare.";
    }

    if (empty($source_err) && empty($destination_err) && empty($fare_err)) {
        $stmt = $conn->prepare("INSERT INTO routes (source, destination, fare) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $source, $destination, $fare);
        if ($stmt->execute()) {
            $success_msg = "Route added successfully.";
            $source = $destination = $fare = "";
        } else {
            $success_msg = "Error: Could not add route.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Route</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="styles.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 70px 20px 20px 20px; /* padding top for fixed navbar */
    }
    .form-container {
      max-width: 500px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
    }
    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }
    .error {
      color: red;
      font-size: 13px;
      margin-top: 3px;
    }
    .success {
      color: green;
      font-weight: 600;
      margin-bottom: 15px;
      text-align: center;
    }
    button {
      background-color: #1e88e5;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
    }
    button:hover {
      background-color: #1565c0;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
  <h2 style="text-align: center; margin-bottom: 20px;">Add Route</h2>

  <?php if ($success_msg): ?>
    <div class="success"><?php echo htmlspecialchars($success_msg); ?></div>
  <?php endif; ?>

  <form method="post" action="add_route.php" novalidate>
    <div class="form-group">
      <label for="source">Source</label>
      <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($source); ?>" required />
      <?php if ($source_err): ?><div class="error"><?php echo htmlspecialchars($source_err); ?></div><?php endif; ?>
    </div>

    <div class="form-group">
      <label for="destination">Destination</label>
      <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($destination); ?>" required />
      <?php if ($destination_err): ?><div class="error"><?php echo htmlspecialchars($destination_err); ?></div><?php endif; ?>
    </div>

    <div class="form-group">
      <label for="fare">Fare</label>
      <input type="number" step="0.01" id="fare" name="fare" value="<?php echo htmlspecialchars($fare); ?>" required />
      <?php if ($fare_err): ?><div class="error"><?php echo htmlspecialchars($fare_err); ?></div><?php endif; ?>
    </div>

    <button type="submit">Submit</button>
  </form>
</div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      console.log('Toggle script running');
      var toggleButton = document.querySelector('.navbar-toggler');
      var menu = document.getElementById('customMenu');
      console.log('Toggle button:', toggleButton);
      console.log('Menu:', menu);
      if (toggleButton && menu) {
        toggleButton.addEventListener('click', function () {
          console.log('Toggle button clicked');
          if (menu.classList.contains('show')) {
            menu.classList.remove('show');
            menu.style.display = 'none';
          } else {
            menu.classList.add('show');
            menu.style.display = 'block';
          }
        });
      }
    });
  </script>
  
</body>
</html>
