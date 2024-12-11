<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: view_posts.php');
    exit();
}

$comment_id = $_GET['id'];

// Fetch the comment
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :id");
$stmt->execute(['id' => $comment_id]);
$comment = $stmt->fetch();

// Check if the user owns the comment or is an admin
if (!$comment || ($_SESSION['user_id'] != $comment['user_id'] && $_SESSION['role'] != 'admin')) {
    die("You do not have permission to edit this comment.");
}

// Update the comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_comment = trim($_POST['comment']);
    if (!empty($updated_comment)) {
        $stmt = $pdo->prepare("UPDATE comments SET comment = :comment WHERE id = :id");
        $stmt->execute(['comment' => $updated_comment, 'id' => $comment_id]);
        
        // Redirect to the post the comment belongs to
        header("Location: single_post.php?id=" . $comment['post_id']);
        exit();
    } else {
        $error = "Comment cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h1 {
            text-align: center;
            font-size: 1.5rem;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            resize: none;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Comment</h1>
        <form action="edit_comment.php?id=<?php echo $comment_id; ?>" method="post">
            <textarea name="comment" rows="5" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
            <button type="submit">Update Comment</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
