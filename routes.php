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

$routes = [];
$sql = "SELECT * FROM routes ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Routes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="styles.css" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="datatable-code.js"></script>
  <script type="text/javascript" src="datatable-function.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 70px 20px 20px 20px; /* padding top for fixed navbar */
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    table th, table td {
      padding: 12px;
      text-align: center;
      border: 1px solid #ddd;
    }
    table th {
      background-color: #1e88e5;
      color: white;
    }

    html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  background-color: #f4f4f4;
  display: flex;
  flex-direction: column;
}

body {
  padding-top: 70px; /* for fixed navbar */
}

main {
  flex: 1;
}


    /* Footer */
    footer {
      background: rgba(0, 0, 0, 0.7);
      color: white;
      text-align: center;
      padding: 20px 10px;
    }

    .social-icons {
      margin-top: 10px;
    }

    .social-icons a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      font-size: 18px;
      display: inline-flex;
      align-items: center;
      transition: color 0.3s ease;
    }

    .social-icons a:hover {
      color: #ff4081;
    }

    .social-icons i {
      margin-right: 5px;
    }
  </style>
</head>
<body style="padding-top: 70px;">

<?php
include 'navbar.php';
?>
<main>
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-weight: 700; color: black; margin: 0;">Routes</h2>
    <a href="add_route.php" style="background-color: #1e88e5; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 600;">Add Route</a>
  </div>

  <table id="routesTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Fare</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($routes as $route) {
          echo '<tr>
                  <td>' . $route['id'] . '</td>
                  <td>' . $route['source'] . '</td>
                  <td>' . $route['destination'] . '</td>
                  <td>' . $route['fare'] . '</td>
                  <td>
                    <a href="edit_route.php?id=' . $route['id'] . '" style="background-color: #4caf50; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin-right: 5px;">Edit</a>
                    <a href="delete_route.php?id=' . $route['id'] . '" style="background-color: #f44336; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none;" onclick="return confirm(\'Are you sure you want to delete this route?\');">Delete</a>
                  </td>
                </tr>';
      }
      ?>
    </tbody>
  </table>
</main>


<!-- Footer -->
<footer>
    &copy; 2025 SR Bus. All rights reserved.
    <div class="social-icons">
      <a href="#"><i class="fab fa-instagram"></i>SR Bus</a>
      <a href="#"><i class="fab fa-facebook"></i>SR Bus</a>
    </div>
  </footer>
</body>
</html>
