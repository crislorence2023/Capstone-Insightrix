<?php
$email = "coe3@gmail.com"; // Use one of your actual emails
$password = "P@silang09"; // The password you're trying to use

// Connect to database
include 'db_connect.php';

// Get the stored hash
$stmt = $conn->prepare("SELECT password FROM coe_staff WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo "Testing login for: " . $email . "<br>";
echo "Stored hash: " . $data['password'] . "<br>";
echo "Password verification result: " . (password_verify($password, $data['password']) ? "Valid" : "Invalid") . "<br>";

// Generate a new hash for this password
echo "New hash for this password: " . password_hash($password, PASSWORD_DEFAULT) . "<br>";
?> 