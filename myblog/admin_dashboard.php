<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Fetch all users from the database
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

// Fetch all messages from the database
$stmt_messages = $pdo->query("SELECT * FROM messages");
$messages = $stmt_messages->fetchAll();

// Fetch all modules from the database
$stmt_modules = $pdo->query("SELECT * FROM modules");
$modules = $stmt_modules->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-reply {
            background-color: #28a745;
        }
        .btn-dashboard {
            background-color: #17a2b8; /* Light blue for navigation buttons */
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <p>Welcome, Admin! <a href="logout.php">Log Out</a></p>
    
    <!-- Add button to redirect to view_posts.php -->
    <a href="view_posts.php" class="btn btn-dashboard">View Posts</a>

    <h2>Manage Users</h2>
    <a href="add_user.php" class="btn">Add New User</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn">Edit</a> |
                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>View Messages</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Sent At</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($messages as $message): ?>
        <tr>
            <td><?php echo htmlspecialchars($message['id']); ?></td>
            <td><?php echo htmlspecialchars($message['name']); ?></td>
            <td><?php echo htmlspecialchars($message['email']); ?></td>
            <td><?php echo htmlspecialchars($message['subject']); ?></td>
            <td><?php echo htmlspecialchars($message['message']); ?></td>
            <td><?php echo htmlspecialchars($message['sent_at']); ?></td>
            <td>
                <a href="reply_message.php?id=<?php echo $message['id']; ?>" class="btn btn-reply">Reply</a> |
                <a href="delete_message.php?id=<?php echo $message['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Manage Modules</h2>
    <a href="add_module.php" class="btn">Add New Module</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Module Name</th>
            <th>Actions</th>
        </tr>
        
        <?php foreach ($modules as $module): ?>
        <tr>
            <td><?php echo htmlspecialchars($module['id']); ?></td>
            <td><?php echo htmlspecialchars($module['module_name']); ?></td>
            <td>
                <a href="edit_module.php?id=<?php echo $module['id']; ?>" class="btn">Edit</a> |
                <a href="delete_module.php?id=<?php echo $module['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this module?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
