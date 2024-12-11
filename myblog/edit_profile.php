<?php
// You can include your authentication check or session logic here if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data, like updating the user's profile
    // Include your logic to handle the form submission
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Example validation
    if ($new_password !== $confirm_new_password) {
        $error = "Passwords do not match!";
    } else {
        // Handle profile update (e.g., save to the database)
        // Update logic goes here
        $success = "Profile updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container */
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        /* Heading */
        h2 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Form Labels */
        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #555;
        }

        /* Input Fields */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #0000FF; /* Pure blue */
        }

        /* Buttons */
        button, a {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 12px;
        }

        button {
            background-color: #0000FF; /* Pure blue */
            color: white;
            border: none;
        }

        button:hover {
            background-color: #0000CC; /* Darker blue */
        }

        a.btn-view_posts {
            background-color: #f3f4f6;
            color: #333;
            border: 1px solid #ddd;
        }

        a.btn-view_posts:hover {
            background-color: #e0e2e9;
        }

        a.logout-btn {
            background-color: #FF0000; /* Pure red */
            color: white;
            border: none;
            margin-top: 8px;
        }

        a.logout-btn:hover {
            background-color: #CC0000; /* Darker red */
        }

        /* Success/ Error Messages */
        .success, .error {
            font-size: 14px;
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <!-- Display error or success message if set -->
        <?php if (isset($error)): ?>
            <p class="error"><?= $error; ?></p> <!-- Display error message -->
        <?php elseif (isset($success)): ?>
            <p class="success"><?= $success; ?></p> <!-- Display success message -->
        <?php endif; ?>

        <!-- Profile update form -->
        <form action="edit_profile.php" method="POST">
            <!-- Full Name field, pre-filled with current value if available -->
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" value="<?= isset($full_name) ? $full_name : ''; ?>" required>

            <!-- Email field, pre-filled with current value if available -->
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= isset($email) ? $email : ''; ?>" required>

            <!-- Current Password field -->
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>

            <!-- New Password field -->
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <!-- Confirm New Password field -->
            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" name="confirm_new_password" id="confirm_new_password" required>

            <!-- Submit button to update profile -->
            <button type="submit" class="btn-update">Update Profile</button>
        </form>

        <!-- Navigation links -->
        <a href="view_posts.php" class="btn-view_posts">Back to View Post</a> <!-- Link to view posts -->
        <a href="logout.php" class="logout-btn">Log Out</a> <!-- Log out link -->
    </div>
</body>
</html>