<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

require 'db.php'; // Include database connection

// Initialize error message variable
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        $errorMessage = 'Please enter both email and password.';
    } else {
        try {
            // Prepare and execute query to fetch user data based on email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Start session and set user details
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to the admin dashboard for admin users
                if ($user['role'] == 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    // Redirect to a user dashboard or homepage for regular users
                    header('Location: view_posts.php');
                }
                exit();
            } else {
                $errorMessage = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuddyBlog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>BuddyBlog</h1>

    <!-- Display error message if there's any -->
    <?php if ($errorMessage): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email" />
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password" />
        </div>

        <button type="submit" class="btn">Log In</button>
    </form>

    <p style="text-align:center; margin-top: 10px;">Don't have an account? <a href="register.php">Register</a></p>
    <p style="text-align:center; margin-top: 10px;">Have an issue? <a href="contact_form.html">Contact Us</a></p>
</div>

</body>
</html>
