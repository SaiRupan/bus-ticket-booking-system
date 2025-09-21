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

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$from_date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
$to_date = isset($_GET['to_date']) ? $conn->real_escape_string($_GET['to_date']) : '';

$bookings = [];
$sql = "SELECT * FROM bookings WHERE 1=1";

if ($search !== '') {
    $sql .= " AND (name LIKE '%$search%' OR bus_name LIKE '%$search%' OR source LIKE '%$search%' OR destination LIKE '%$search%')";
}

if ($from_date !== '') {
    if ($to_date !== '') {
        $sql .= " AND date BETWEEN '$from_date' AND '$to_date'";
    } else {
        // If only from_date is selected, show from that date to today's date
        $sql .= " AND date BETWEEN '$from_date' AND CURDATE()";
    }
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="datatable-code.js"></script>
  <link rel="stylesheet" type="text/css" href="styles.css">
  <script type="text/javascript" src="datatable-function.js"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
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
      padding: 20px;
      margin-top: 80px;
    }

    .filters {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .filters input,
    .filters button {
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .filters button {
      background-color: #1e88e5;
      color: white;
      border: none;
      cursor: pointer;
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

    

    .approve-btn {
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
    }

    .approve-btn:hover {
      background-color: #45a049;
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
// Removed $hideDashboardLogout to allow navbar links to show
include 'navbar.php';
?>

  <div class="dashboard-container" style="margin-top: 0;">
    <h2 style="text-align: center; margin: 10px 0 20px 0; font-weight: 700; color: black;">BOOKINGS</h2>
    <form method="GET" action="admin_dashboard.php" class="filters" style="display:flex; justify-content: space-between; margin-bottom: 20px;">
      
    <div style="display: flex; align-items: center; gap: 10px;">
  <label for="dateFilter">From:</label>
  <input type="date" name="date" id="dateFilter" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : ''; ?>">

  <label for="toDateFilter">To:</label>
  <input type="date" name="to_date" id="toDateFilter" value="<?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : ''; ?>">

  <button type="button" id="resetDatesBtn" style="background-color: #f44336; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">Reset</button>
</div>

      <button type="button" onclick="window.location.href='admin_dashboard.php'">Refresh</button>
      <button type="button" onclick="generateReport()">Report</button>
    </form>
    <script>
      // Remove the refresh button as page reload will refresh the page
      document.addEventListener("DOMContentLoaded", function() {
        const refreshButton = document.querySelector("button[onclick=\"window.location.href='admin_dashboard.php'\"]");
        if (refreshButton) {
          refreshButton.remove();
        }
        document.getElementById('dateFilter').addEventListener('change', function() {
          this.form.submit();
        });
        document.getElementById('toDateFilter').addEventListener('change', function() {
          const fromDate = document.getElementById('dateFilter').value;
          const errorElemId = 'dateError';
          let errorElem = document.getElementById(errorElemId);
          if (!fromDate) {
            if (!errorElem) {
              errorElem = document.createElement('div');
              errorElem.id = errorElemId;
              errorElem.style.color = 'red';
              errorElem.style.marginTop = '5px';
              errorElem.style.fontSize = '14px';
              errorElem.textContent = 'Please select From date first.';
              this.parentNode.appendChild(errorElem);
            }
            this.value = '';
            return;
          } else {
            if (errorElem) {
              errorElem.remove();
            }
          }
          this.form.submit();
        });

        // Add reset button functionality
        const resetBtn = document.getElementById('resetDatesBtn');
        if (resetBtn) {
          resetBtn.addEventListener('click', function() {
            document.getElementById('dateFilter').value = '';
            document.getElementById('toDateFilter').value = '';
            this.form.submit();
          });
        }
      });
    </script>

    <table id="userTable">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Name</th>
          <th>Source</th>
          <th>Destination</th>
          <th>Date</th>
          <th>price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="bookingTableBody">
  <?php
  $i = 1;
      foreach ($bookings as $booking) {
          echo "<tr>
                  <td>{$i}</td>
                  <td>{$booking['name']}</td>
                  <td>{$booking['source']}</td>
                  <td>{$booking['destination']}</td>
                  <td>{$booking['date']}</td>
                  <td>{$booking['price']}</td>
                  <td>";
          if ($booking['status'] === 'Confirmed') {
              echo "<span style='color: green; font-weight: bold;'>Approved</span>";
          } elseif ($booking['status'] === 'Rejected') {
              echo "<span style='color: red; font-weight: bold;'>Rejected</span>";
          } else {
              echo "<form method='POST' action='approve_booking_fixed.php' style='display:inline;'>
                      <input type='hidden' name='booking_id' value='{$booking['id']}'>
                      <button type='button' class='approve-btn'>Approve</button>
                      <button type='button' class='reject-btn' style='background-color: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-left: 5px;'>Reject</button>
                    </form>";
          }
          echo "</td></tr>";
          $i++;
      }
  ?>
</tbody>
    </table>
  </div>

  <script>
    function logout() {
      window.location.href = 'logout.php';
    }

    function generateReport() {
      const fromDate = document.getElementById('dateFilter').value;
      const toDate = document.getElementById('toDateFilter').value;

      if (!fromDate && !toDate) {
        // No dates selected, download all bookings excluding rejected
        window.open('report.php?exclude_rejected=1', '_blank');
        return;
      }

      if (!fromDate) {
        alert('Please select a from date to generate the report.');
        return;
      }
      if (fromDate && !toDate) {
        alert('Please select a To date to generate the report.');
        return;
      }
      if (toDate) {
        if (toDate < fromDate) {
          alert('To date cannot be earlier than from date.');
          return;
        }
        // Open report.php with from_date and to_date to trigger CSV download, excluding rejected bookings
        window.open('report.php?from_date=' + encodeURIComponent(fromDate) + '&to_date=' + encodeURIComponent(toDate) + '&exclude_rejected=1', '_blank');
      } else {
        // Open report.php with the selected single date to trigger CSV download, excluding rejected bookings
        window.open('report.php?date=' + encodeURIComponent(fromDate) + '&exclude_rejected=1', '_blank');
      }
    }

    $(document).ready(function() {
      var formToSubmit = null;
      var bookingId = null;
      var actionType = null; // 'approve' or 'reject'

      $(document).on('click', '.approve-btn', function(e) {
        e.preventDefault();
        formToSubmit = $(this).closest('form');
        bookingId = formToSubmit.find('input[name="booking_id"]').val();
        actionType = 'approve';
        $('#confirmModalMessage').text('Are you sure want to confirm?');
        $('#confirmModal').addClass('show');
      });

      $(document).on('click', '.reject-btn', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        bookingId = form.find('input[name="booking_id"]').val();
        actionType = 'reject';
        $('#confirmModalMessage').text('Are you sure want to Reject?');
        $('#confirmModal').addClass('show');
      });

      $('#modalCancel').click(function() {
        $('#confirmModal').removeClass('show');
        // Refresh the page on cancel click only for reject action
        if (actionType === 'reject') {
          location.reload();
        }
      });

      $('#modalOk').click(function() {
        $('#confirmModal').removeClass('show');
        if (actionType === 'approve' && formToSubmit) {
          // Directly submit the form
          formToSubmit.submit();
        } else if (actionType === 'reject' && bookingId) {
          // Send AJAX request to reject booking
          $.ajax({
            url: 'reject_booking.php',
            type: 'POST',
            data: { booking_id: bookingId },
            success: function(response) {
              // Update the action cell in the table row
              var row = $('input[name="booking_id"][value="' + bookingId + '"]').closest('tr');
              row.find('td:last').html('<span style="color: red; font-weight: bold;">Rejected</span>');
            },
            error: function() {
              alert('Failed to reject the booking.');
            }
          });
        }
      });
    });
  </script>

  <!-- Confirmation Modal -->
  <style>
  #confirmModal {
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
    #confirmModal.show {
      display: block;
      opacity: 1;
    }
    #confirmModal .modal-content {
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
    #confirmModal p {
      font-size: 18px;
      margin-bottom: 25px;
      font-weight: 600;
    }
    #confirmModal .modal-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    #confirmModal button {
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
      background-color: #4caf50;
      color: white;
    }
    #modalOk:hover {
      background-color: #45a049;
    }
  </style>
  <div id="confirmModal">
    <div class="modal-content">
      <p id="confirmModalMessage">Are you sure?</p>
      <div class="modal-buttons">
        <button id="modalCancel">Cancel</button>
        <button id="modalOk">OK</button>
      </div>
    </div>
  </div>

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
