<?php
require 'db.php'; // Include the database connection

// Admin data
$username = 'admin';
$email = 'admin@gmail.com';
$password = 'Muito1234'; // Plain-text password

// Hash the password before inserting
$hashed_password = password_hash($password, PASSWORD_DEFAULT);  // This generates the hash

try {
    // Insert admin data into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role)
    VALUES (:username, :email, :password, 'admin')");

    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password  // Store the hashed password
    ]);

    echo "Admin account created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
