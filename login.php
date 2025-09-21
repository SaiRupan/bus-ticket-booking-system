<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Bus Booking</title>
    <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body, html {
          height: 100%;
          font-family: 'Poppins', sans-serif;
          background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                      url('https://images.unsplash.com/photo-1570125909517-53cb21c89ff2?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center/cover;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          color: white;
        }
        nav {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 20px 40px;
          background: rgba(0, 0, 0, 0.5);
        }
        nav h1 {
          color: #ffffff;
          font-size: 28px;
          font-weight: 600;
        }
        /* Removed login button */
        .hero {
          flex: 1;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;
          color: white;
          padding: 0 20px;
        }
        .hero h2 {
          font-size: 48px;
          font-weight: bold;
          text-shadow: 2px 2px 4px #000;
        }
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
        @media (max-width: 600px) {
          .hero h2 {
            font-size: 32px;
          }
          nav {
            flex-direction: column;
            align-items: flex-start;
          }
        }
        .login-box {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
            color: black;
            margin: 80px auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: #4285f4;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #3367d6;
        }
        .link {
            text-align: center;
            margin-top: 15px;
            color: black;
        }
        .link a {
            text-decoration: none;
            color: #4285f4;
        }
    </style>
</head>
<body>

  <!-- Navigation -->
  <nav>
    <a class="navbar-brand" href="index.html" style="font-size: 28px; font-weight: bold; color: #ffffff;">
      🚌  <span style="color: #ffdd00;">SR</span><span style="color: #ffffff;">Bus</span>
    </a>
  </nav>

  <div class="login-box">
    <h2>Login</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'registered'): ?>
        <div class="success-message">Registration successful! Please login.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
        <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
            Invalid email or password. Please try again.
        </div>
    <?php endif; ?>

    <form action="login_submit.php" method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>

    <div class="link">
        Don't have an account? <a href="register.php">Register here</a>
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
