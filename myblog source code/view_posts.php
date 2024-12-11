<?php
session_start();
require 'db.php'; // Database connection

// Check if the user is logged in, if not, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the user is an admin
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Handle delete post request
if (isset($_GET['delete_post_id'])) {
    $post_id = $_GET['delete_post_id'];

    // Check if the user owns the post or is an admin
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = :id");
    $stmt->execute(['id' => $post_id]);
    $post_owner = $stmt->fetch();

    if ($post_owner && ($_SESSION['user_id'] == $post_owner['user_id'] || $isAdmin)) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->execute(['id' => $post_id]);
        header("Location: view_posts.php");
        exit();
    } else {
        $error = "You don't have permission to delete this post.";
    }
}

// Fetch posts with their associated user and comments
$stmt = $pdo->prepare("
    SELECT posts.*, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();

// Fetch comments for each post
$stmt_comments = $pdo->prepare("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = :post_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .header .buttons {
            display: flex;
            gap: 10px;
        }
        .header a {
            text-decoration: none;
            padding: 8px 15px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
        }
        .header a.logout-btn {
            background-color: #dc3545;
        }
        .header a:hover {
            opacity: 0.9;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .post {
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
        }
        .post h3 {
            margin: 0;
            font-size: 1.25rem;
        }
        .post p.meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .post p.content {
            margin: 10px 0;
        }
        .post img, .post video {
            width: 30%;
            max-width: 200px;
            margin-top: 10px;
        }
        .actions a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }
        .actions a.delete-btn {
            color: #dc3545
        }
        .header a.admin-btn {
        background-color: #28a745;
        color: #fff;
        border-radius: 5px;
        padding: 8px 15px;
        text-decoration: none;
        }
        .header a.admin-btn:hover {
        opacity: 0.9;
        }

</style>
</head>
<body>
    <!-- Header with welcome message and navigation buttons -->
    <div class="header"> 
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</h1> 
        <div class="buttons">
            <a href="create_post.php" class="button">Create Post</a>
            <a href="notifications.php">Notifications</a>
            <a href="contact_form.html">Contact Admin</a>
            <a href="edit_profile.php">Edit Profile</a>
            <?php if ($isAdmin): ?>
                <a href="admin_dashboard.php" class="button admin-btn">Admin Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Posts container -->
    <div class="container">
        <h2>Posts</h2>

        <!-- Display posts -->
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p class="meta">By: <?php echo htmlspecialchars($post['username']); ?> | Date: <?php echo $post['created_at']; ?></p>
                <p class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                
                <!-- Show media if attached -->
                <?php if (!empty($post['file_path'])): ?>
                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['file_path'])): ?>
                        <img src="<?php echo htmlspecialchars($post['file_path']); ?>" alt="Post Image">
                    <?php elseif (preg_match('/\.(mp4|avi|mov|mkv)$/i', $post['file_path'])): ?>
                        <video controls>
                            <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Post actions -->
                <div class="actions">
                    <a href="single_post.php?id=<?php echo $post['id']; ?>">View</a>
                    <?php if ($_SESSION['user_id'] == $post['user_id'] || $isAdmin): ?>
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                        <a href="view_posts.php?delete_post_id=<?php echo $post['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
