<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Check if the form is submitted to add a new module
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['module_name'])) {
    $module_name = trim($_POST['module_name']);

    if (!empty($module_name)) {
        // Prepare and execute the insert query
        $stmt = $pdo->prepare("INSERT INTO modules (module_name) VALUES (:module_name)");
        $stmt->bindParam(':module_name', $module_name);
        $stmt->execute();
        
        // Redirect to the admin dashboard after adding
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Module name cannot be empty.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Module</title>
    <style>
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back {
            background-color: #28a745;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

    <h1>Add New Module</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="add_module.php" method="POST">
        <label for="module_name">Module Name: </label>
        <input type="text" id="module_name" name="module_name" required>
        <button type="submit" class="btn">Add Module</button>
    </form>

    <a href="admin_dashboard.php" class="btn btn-back">Back to Dashboard</a>

</body>
</html>