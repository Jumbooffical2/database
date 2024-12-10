<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Fetch notifications for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Notifications</h1>
        
        <!-- Back to Posts Button -->
        <a href="view_posts.php" class="back-button">Back to Posts</a>

        <?php if (count($notifications) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Message</th>
                    <th>Received At</th>
                </tr>

                <?php foreach ($notifications as $notification): ?>
                <tr>
                    <td><?php echo htmlspecialchars($notification['id']); ?></td>
                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
