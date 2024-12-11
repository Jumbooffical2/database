<?php 
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Validate and fetch post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid post ID.');
}

$post_id = (int)$_GET['id'];

// Fetch the single post along with its module information
$stmt = $pdo->prepare("
    SELECT posts.*, users.username, modules.module_name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    JOIN modules ON posts.module_id = modules.id 
    WHERE posts.id = :id
");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch();

if (!$post) {
    die('Post not found.');
}

// Fetch comments
$stmt_comments = $pdo->prepare("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = :post_id
");
$stmt_comments->execute([':post_id' => $post_id]);
$comments = $stmt_comments->fetchAll();

// Check if the user is an admin
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        $stmt = $pdo->prepare("
            INSERT INTO comments (post_id, user_id, comment, created_at) 
            VALUES (:post_id, :user_id, :comment, NOW())
        ");
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id,
            ':comment' => $comment
        ]);
        header("Location: single_post.php?id=$post_id");
        exit();
    } else {
        $error = "Comment cannot be empty.";
    }
}

// Handle delete comment request
if (isset($_GET['delete_comment_id'])) {
    $comment_id = $_GET['delete_comment_id'];

    // Check if the user owns the comment or is an admin
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
    $stmt->execute(['id' => $comment_id]);
    $comment_owner = $stmt->fetch();

    if ($comment_owner && ($_SESSION['user_id'] == $comment_owner['user_id'] || $isAdmin)) {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->execute(['id' => $comment_id]);
        header("Location: single_post.php?id=$post_id");
        exit();
    } else {
        $error = "You don't have permission to delete this comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 85%;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        h2 {
            font-size: 2em;
            color: #333;
        }

        .meta {
            font-size: 0.9em;
            color: #777;
            margin-top: 10px;
        }

        .meta strong {
            color: #444;
        }

        .content {
            font-size: 1.1em;
            line-height: 1.6;
            margin: 20px 0;
        }

        img, video {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .comments h4 {
            margin-top: 40px;
            font-size: 1.5em;
            color: #333;
        }

        .comments p {
            background-color: #f4f4f4;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 1em;
        }

        .comments .actions a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }

        .comments .actions a:hover {
            text-decoration: underline;
        }

        .comments .delete {
            color: red;
            font-weight: bold;
        }

        .add-comment textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            font-size: 1em;
            resize: vertical;
        }

        .add-comment button {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
        }

        .add-comment button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            font-size: 1em;
            margin-top: 10px;
        }
</style>
</head>
<body>
    <div class="container">
        <!-- Link to navigate back to the list of posts -->
        <a href="view_posts.php" class="back-button">&larr; Back to Posts</a>

        <!-- Display the title of the post -->
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>

        <!-- Display post metadata: author, creation date, and module name -->
        <p class="meta">
            By <?php echo htmlspecialchars($post['username']); ?> | <?php echo $post['created_at']; ?>
            <br>
            <strong>Module:</strong> <?php echo htmlspecialchars($post['module_name']); ?>
        </p>

        <!-- Display the content of the post -->
        <p class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

        <!-- Display attached media (image or video) if available -->
        <?php if (!empty($post['file_path'])): ?>
            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['file_path'])): ?>
                <img src="<?php echo htmlspecialchars($post['file_path']); ?>" alt="Post Image">
            <?php elseif (preg_match('/\.(mp4|avi|mov|mkv)$/i', $post['file_path'])): ?>
                <video controls>
                    <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="video/mp4">
                </video>
            <?php endif; ?>
        <?php endif; ?>

        <div class="comments">
            <h4>Comments</h4>

            <!-- Loop through and display all comments for the post -->
            <?php foreach ($comments as $comment): ?>
                <p>
                    <!-- Display commenter's username and comment -->
                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?>

                    <!-- Show Edit/Delete options if the user is the owner or an admin -->
                    <?php if ($_SESSION['user_id'] == $comment['user_id'] || $isAdmin): ?>
                        <span class="actions">
                            <a href="edit_comment.php?id=<?php echo $comment['id']; ?>">Edit</a>
                            <a href="single_post.php?id=<?php echo $post_id; ?>&delete_comment_id=<?php echo $comment['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                        </span>
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>

            <!-- Form to add a new comment -->
            <div class="add-comment">
                <form action="single_post.php?id=<?php echo $post_id; ?>" method="post">
                    <textarea name="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
                <!-- Display error message if any -->
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </div>
        </div>
    </div>
</body>
</html>

