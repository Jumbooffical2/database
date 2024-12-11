<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Include the database connection
require 'db.php';

// Check if the message ID is provided
if (!isset($_GET['id'])) {
    echo "No message ID provided.";
    exit();
}

$message_id = $_GET['id'];

try {
    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute(['id' => $message_id]);

    // Redirect back to the admin_dashboard.php page
    header("Location: admin_dashboard.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
