<?php
// Include your database connection
include('db_connection.php');

// Start session to check if the user is an admin
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login or error page if not an admin
    header("Location: login.php");
    exit();
}

// Check if user ID is provided for deletion
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare the SQL query to delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // 'i' denotes the integer type for user_id

    // Execute the query and check if the deletion was successful
    if ($stmt->execute()) {
        $success_message = "User account deleted successfully.";
    } else {
        $error_message = "Error deleting user account. Please try again.";
    }

    // Close the statement
    $stmt->close();
} else {
    $error_message = "User ID is missing.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS here -->
</head>
<body>
    <div class="container">
        <h2>Delete User Account</h2>

        <?php if (isset($success_message)): ?>
            <p class="success"><?= $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>

        <!-- Optionally, you can include a button to go back to the users list or dashboard -->
        <a href="admin_dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
