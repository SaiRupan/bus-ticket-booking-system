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

$users = [];
$sql = "SELECT id, fullname, phone, email FROM users ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Users</title>
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
<body>

<?php include 'navbar.php'; ?>

<main>
<h2 style="text-align: center; margin-bottom: 20px; font-weight: 700; color: black;">Users</h2>

<table id="usersTable">
  <thead>
    <tr>
      <th>ID</th>
      <th>fullname</th>
      <th>phone</th>
      <th>email</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($users as $user) {
        echo '<tr>
                <td>' . htmlspecialchars($user['id']) . '</td>
                <td>' . htmlspecialchars($user['fullname']) . '</td>
                <td>' . htmlspecialchars($user['phone']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>
                  <form method="POST" action="delete_user.php" class="deleteUserForm" style="display:inline;">
                    <input type="hidden" name="user_id" value="' . htmlspecialchars($user['id']) . '">
                    <button type="button" class="deleteBtn" style="background-color: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Delete</button>
                  </form>
                </td>
              </tr>';
    }
    ?>
  </tbody>
</table>
</main>

<!-- Delete Confirmation Modal -->
<style>
#deleteConfirmModal {
  display: none;
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  transition: opacity 0.3s ease;
}
#deleteConfirmModal.show {
  display: block;
  opacity: 1;
}
#deleteConfirmModal .modal-content {
  background-color: #fff;
  margin: 0 auto;
  padding: 20px 30px;
  border-radius: 10px;
  width: 320px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  text-align: center;
  font-family: 'Poppins', sans-serif;
  color: #333;
  animation: fadeInScale 0.3s ease forwards;
  position: relative;
  top: 10%;
}
@keyframes fadeInScale {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}
#deleteConfirmModal p {
  font-size: 18px;
  margin-bottom: 25px;
  font-weight: 600;
}
#deleteConfirmModal .modal-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
}
#deleteConfirmModal button {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-weight: 600;
  min-width: 100px;
  margin: 0;
}
#modalCancel {
  background-color: #e0e0e0;
  color: #555;
}
#modalCancel:hover {
  background-color: #d5d5d5;
}
#modalOk {
  background-color: #f44336;
  color: white;
}
#modalOk:hover {
  background-color: #d32f2f;
}
</style>

<div id="deleteConfirmModal">
  <div class="modal-content">
    <p>Are you sure you want to delete this user?</p>
    <div class="modal-buttons">
      <button id="modalCancel">Cancel</button>
      <button id="modalOk">Delete</button>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  $('#usersTable').DataTable({
    pageLength: 5, // Optional
    lengthChange: false // Optional to hide page length dropdown
  });

  let formToSubmit = null;

  // Use event delegation for dynamically created elements
  $('#usersTable').on('click', '.deleteBtn', function() {
    formToSubmit = $(this).closest('form');
    $('#deleteConfirmModal').addClass('show');
  });

  $('#modalCancel').on('click', function() {
    $('#deleteConfirmModal').removeClass('show');
    formToSubmit = null;
  });

  $('#modalOk').on('click', function() {
    if (formToSubmit) {
      formToSubmit.submit();
    }
  });
});
</script>


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
