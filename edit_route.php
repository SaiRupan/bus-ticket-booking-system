<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "leooffice", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$route_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($route_id <= 0) {
    header("Location: routes.php");
    exit();
}

$route = null;
$sql = "SELECT * FROM routes WHERE id = $route_id LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows === 1) {
    $route = $result->fetch_assoc();
} else {
    header("Location: routes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $conn->real_escape_string($_POST['source']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $fare = floatval($_POST['fare']);

    if (strcasecmp($source, $destination) == 0) {
        $error = "Source and destination cannot be the same.";
    } else {
        $update_sql = "UPDATE routes SET source='$source', destination='$destination', fare=$fare WHERE id=$route_id";
        if ($conn->query($update_sql) === TRUE) {
            header("Location: routes.php");
            exit();
        } else {
            $error = "Error updating route: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Route</title>
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
      max-width: 600px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }
    input[type="text"], input[type="number"] {
      width: 100%;
      padding: 8px 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      background-color: #4caf50;
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
    }
    button:hover {
      background-color: #45a049;
    }
    .error {
      color: red;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
  <h2>Edit Route</h2>
  <?php if (!empty($error)) : ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="POST" action="" id="editRouteForm">
    <label for="source">Source</label>
    <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($route['source']); ?>" required />

    <label for="destination">Destination</label>
    <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($route['destination']); ?>" required />
    <div id="inlineError" class="error" style="display:none; margin-bottom: 15px;"></div>

    <label for="fare">Fare</label>
    <input type="number" step="0.01" id="fare" name="fare" value="<?php echo htmlspecialchars($route['fare']); ?>" required />

    <button type="submit">Update Route</button>
  </form>
</div>

<script>
document.getElementById('editRouteForm').addEventListener('submit', function(e) {
    const source = document.getElementById('source').value.trim().toLowerCase();
    const destination = document.getElementById('destination').value.trim().toLowerCase();
    const inlineError = document.getElementById('inlineError');
    if (source === destination) {
        e.preventDefault();
        inlineError.textContent = 'Source and destination cannot be the same.';
        inlineError.style.display = 'block';
    } else {
        inlineError.textContent = '';
        inlineError.style.display = 'none';
    }
});
</script>

</body>
</html>
