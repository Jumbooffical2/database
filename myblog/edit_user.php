<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Get the user ID from the URL
$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Update user in the database
    try {
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'id' => $user_id
        ]);

        echo "User updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<h1>Edit User</h1>
<form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>">
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
    <select name="role" required>
        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
    </select><br>
    <button type="submit">Update User</button>
</form>
