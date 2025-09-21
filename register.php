<!DOCTYPE html>
<html>
<head>
    <title>Register - Bus Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
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

        .error {
            color: red;
            font-size: 14px;
            margin: 2px 0 5px;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            text-decoration: none;
            color: #4285f4;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Register</h2>

<?php
$fullname_val = isset($_GET['fullname']) ? htmlspecialchars(urldecode($_GET['fullname'])) : '';
$phone_val = isset($_GET['phone']) ? htmlspecialchars(urldecode($_GET['phone'])) : '';
$email_val = isset($_GET['email']) ? htmlspecialchars(urldecode($_GET['email'])) : '';
$email_error_js = isset($_GET['email_error']) ? json_encode(urldecode($_GET['email_error'])) : 'null';
$phone_error_js = isset($_GET['phone_error']) ? json_encode(urldecode($_GET['phone_error'])) : 'null';
?>

    <form id="registerForm" action="register_submit.php" method="POST" onsubmit="return validateForm()">
        <input type="text" name="fullname" id="fullname" placeholder="Full Name" value="<?php echo $fullname_val; ?>">
        <div class="error" id="fullnameError"></div>

        <input type="tel" name="phone" id="phone" placeholder="Phone Number" value="<?php echo $phone_val; ?>">
        <div class="error" id="phoneError"></div>

        <input type="email" name="email" id="email" placeholder="Email" value="<?php echo $email_val; ?>">
        <div class="error" id="emailError"></div>

        <input type="password" name="password" id="password" placeholder="Password">
        <div class="error" id="passwordError"></div>

        <input type="submit" value="Register">
    </form>

<script>
    const serverEmailError = <?php echo $email_error_js; ?>;
    const serverPhoneError = <?php echo $phone_error_js; ?>;
</script>

    <div class="link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<script>
    // On page load, display server-side errors if any
    window.onload = function() {
        if (serverPhoneError) {
            document.getElementById("phoneError").innerText = serverPhoneError;
        }
        if (serverEmailError) {
            document.getElementById("emailError").innerText = serverEmailError;
        }
    };

    function validateForm() {
        let valid = true;

        // Clear error messages only if no server-side error present or field is corrected
        if (!serverPhoneError || (document.getElementById("phone").value.trim() !== "" && /^[6-9]\d{9}$/.test(document.getElementById("phone").value.trim()))) {
            document.getElementById("phoneError").innerText = "";
        }
        if (!serverEmailError || (document.getElementById("email").value.trim() !== "" && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById("email").value.trim()))) {
            document.getElementById("emailError").innerText = "";
        }
        document.getElementById("fullnameError").innerText = "";
        document.getElementById("passwordError").innerText = "";

        // Get values
        const fullname = document.getElementById("fullname").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        // Validate Full Name
        if (fullname === "") {
            document.getElementById("fullnameError").innerText = "Full name is required";
            valid = false;
        }

        // Validate Phone
        const phoneRegex = /^[6-9]\d{9}$/;
        if (phone === "") {
            if (!serverPhoneError) {
                document.getElementById("phoneError").innerText = "Phone number is required";
            }
            valid = false;
        } else if (!phoneRegex.test(phone)) {
            if (!serverPhoneError) {
                document.getElementById("phoneError").innerText = "Invalid phone number";
            }
            valid = false;
        }

        // Validate Email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === "") {
            if (!serverEmailError) {
                document.getElementById("emailError").innerText = "Email is required";
            }
            valid = false;
        } else if (!emailRegex.test(email)) {
            if (!serverEmailError) {
                document.getElementById("emailError").innerText = "Invalid email format";
            }
            valid = false;
        }

        // Validate Password
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;
        if (password === "") {
            document.getElementById("passwordError").innerText = "Password is required";
            valid = false;
        } else if (password.length < 6) {
            document.getElementById("passwordError").innerText = "Password must be at least 6 characters";
            valid = false;
        } else if (!passwordPattern.test(password)) {
            document.getElementById("passwordError").innerText = "Password must include uppercase, lowercase, digit, and special character";
            valid = false;
        }

        return valid;
    }
</script>

</body>
</html>
