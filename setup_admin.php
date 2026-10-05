<?php
// Run ONCE in the browser: http://localhost/tiffin-route/setup_admin.php
// Creates the admin account (admin@tiffinroute.com / admin123). Delete this file afterwards.
require 'config.php';

$email = 'admin@tiffinroute.com';
$hash = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
if ($stmt->get_result()->num_rows) {
    echo 'Admin already exists. You can delete this file.';
    exit;
}
$name = 'Admin';
$phone = '9999999999';
$stmt = $conn->prepare("INSERT INTO users (full_name,email,phone,password_hash,role) VALUES (?,?,?,?,'admin')");
$stmt->bind_param('ssss', $name, $email, $phone, $hash);
$stmt->execute();
echo 'Admin created: admin@tiffinroute.com / admin123. Please delete setup_admin.php now.';
