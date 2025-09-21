<?php
// Script to generate password hash for admin user and output SQL insert statement

$password = 'admin@123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hashed Password: $hashed_password\n\n";

echo "Use the following SQL to insert the admin user into your database:\n";
echo "INSERT INTO users (fullname, phone, email, password) VALUES ('Admin User', '0000000000', 'admin@example.com', '" . $hashed_password . "');\n";
?>
