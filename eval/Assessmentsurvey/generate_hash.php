<?php
$password = "P@silang09"; // The password you want to hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "New hash for this password: " . $hashed_password;
?>