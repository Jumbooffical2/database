<?php
session_start();
require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Optional: If the user is logged in

    // Validate input
    if (empty($subject) || empty($message)) {
        echo "Both subject and message are required.";
        exit();
    }

    // Insert message into database
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, name, email, subject, message, sent_at) 
                               VALUES (:user_id, :name, :email, :subject, :message, NOW())");
        $stmt->execute([
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);

        echo "Your message has been sent successfully!";
        // Optionally redirect after success
        header("Location: view_posts.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
