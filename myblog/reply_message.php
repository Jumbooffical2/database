<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Check if the message ID and reply text are provided
if (isset($_GET['id']) && isset($_POST['reply_message'])) {
    $message_id = $_GET['id'];
    $reply_message = htmlspecialchars($_POST['reply_message']);

    // Fetch the user ID of the original message sender
    $stmt = $pdo->prepare("SELECT user_id FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();

    if (!$message) {
        die('Original message not found.');
    }

    $recipient_user_id = $message['user_id']; // The user who sent the original message

    // Insert the reply notification for the user
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$recipient_user_id, $reply_message]);

    // Optionally: Mark the original message as "replied"
    $stmt = $pdo->prepare("UPDATE messages SET status = 'replied' WHERE id = ?");
    $stmt->execute([$message_id]);

    // Redirect back with a success message
    header('Location: admin_dashboard.php?reply_success=1');
    exit();
}

// Fetch the message details based on ID
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $message = $stmt->fetch();
    if (!$message) {
        die('Message not found.');
    }
} else {
    die('Invalid request.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <style>
        label, textarea, button, p {
            font-family: Arial, sans-serif;
            margin-bottom: 10px;
        }
        textarea {
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>
<body>

    <h1>Reply to Message</h1>

    <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
    <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message['message'])); ?></p>

    <form action="reply_message.php?id=<?php echo $message['id']; ?>" method="POST">
        <label for="reply_message">Your Reply:</label><br>
        <textarea id="reply_message" name="reply_message" rows="4" required></textarea><br>
        <button type="submit">Send Reply</button>
    </form>

    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>

</body>
</html>
