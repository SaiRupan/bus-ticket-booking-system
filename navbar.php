<?php
// Navbar and styles shared by dashboard.php and admin_dashboard.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SRBus Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
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
    /* Custom hamburger icon if default not visible */
    .navbar-toggler-icon {
      display: inline-block;
      width: 30px;
      height: 22px;
      position: relative;
    }
    .navbar-toggler-icon::before,
    .navbar-toggler-icon::after,
    .navbar-toggler-icon div {
      background-color: white;
      position: absolute;
      content: '';
      height: 4px;
      width: 100%;
      border-radius: 2px;
      transition: all 0.2s;
    }
    .navbar-toggler-icon::before {
      top: 0;
    }
    .navbar-toggler-icon div {
      top: 9px;
    }
    .navbar-toggler-icon::after {
      bottom: 0;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-md fixed-top">
    <div class="container" style="position:relative; display:flex; align-items:center; justify-content:space-between;">
      <a class="navbar-brand" href="login.php">SRBus 🚌</a>
      <div class="d-flex justify-content-center flex-grow-1 align-items-end" style="width: 100%; height: 56px;">
        <ul class="navbar-nav d-flex flex-row justify-content-center w-100 align-items-end" style="height: 56px;">
          <?php
          // Determine current page to highlight active link if needed
          $current_page = basename($_SERVER['PHP_SELF']);
          // Remove the check for $hideDashboardLogout to always show links
          if (true) {
              if ($current_page === 'admin_dashboard.php' || $current_page === 'routes.php' || $current_page === 'users.php') {
                  echo '<li class="nav-item px-3"><a class="nav-link" href="my_bookings.php">Bookings</a></li>';
                  echo '<li class="nav-item px-3"><a class="nav-link" href="routes.php">Routes</a></li>';
                  echo '<li class="nav-item px-3"><a class="nav-link" href="users.php">Users</a></li>';
                  // Removed Logout link as per user request
                  // Removed Logout link as per user request
                  // echo '<li class="nav-item px-3"><a class="nav-link" href="logout.php">Logout</a></li>';
              } else {
                  echo '<li class="nav-item px-3"><a class="nav-link" href="my_bookings.php"> bookings</a></li>';
                  echo '<li class="nav-item px-3"><a class="nav-link" href="routes.php">Routes</a></li>';
                  echo '<li class="nav-item px-3"><a class="nav-link" href="users.php">Users</a></li>';
                  
                }
          }
          ?>
        </ul>
      </div>
      <button class="navbar-toggler" type="button" id="customToggle" aria-label="Toggle navigation" style="border:1px solid white; background:#0056b3; color:white; font-size:28px; cursor:pointer; position: relative; z-index: 1100; margin-left:auto; width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
  &#9776;
</button>

<div id="customMenu" style="display: none; position:absolute; right: 0px; top: 56px; background:#ffffff; border-radius:8px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); min-width: 200px; z-index: 1050; border: 1px solid black; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center;">
  <a href="admin_dashboard.php" class="d-block px-4 py-3 text-decoration-none text-center" style="font-weight:600; color:#007bff; border-bottom: 1px solid #eee;">Bookings</a>
  <a href="routes.php" class="d-block px-4 py-3 text-decoration-none text-center" style="font-weight:600; color:#28a745; border-bottom: 1px solid #eee;">Routes</a>
  <a href="users.php" class="d-block px-4 py-3 text-decoration-none text-center" style="font-weight:600; color:#dc3545;">Users</a>
  <a href="logout.php" class="d-block px-4 py-3 text-decoration-none text-center" style="font-weight:600; color:#000000; border-top: 1px solid #eee;">Logout</a>
</div>

    </div>
  </nav>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("customToggle");
    const dropdownMenu = document.getElementById("customMenu");

    toggleBtn.addEventListener("click", function () {
      dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
    });

    // Optional: hide dropdown if clicking outside
    document.addEventListener("click", function (event) {
      if (!toggleBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = "none";
      }
    });
  });
</script>

</body>
</html>
