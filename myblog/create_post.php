<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php'; // Include the database connection

// Fetch all modules from the database
$stmt = $pdo->query("SELECT * FROM modules");
$modules = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $module_id = (int)$_POST['module_id'];
    $user_id = $_SESSION['user_id'];
    $file_path = null;

    // File upload logic
    if (!empty($_FILES['file']['name'])) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . basename($file_name);

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        move_uploaded_file($file_tmp, $file_path);
    }

    // Insert the post into the database
    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, module_id, title, content, file_path, created_at)
        VALUES (:user_id, :module_id, :title, :content, :file_path, NOW())
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':module_id' => $module_id,
        ':title' => $title,
        ':content' => $content,
        ':file_path' => $file_path
    ]);

    // Redirect to view_posts.php
    header('Location: view_posts.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Create Post Form Section -->
        <h1>Create Post</h1>

        <form method="POST" enctype="multipart/form-data">
            <div>
                <!-- Input field for post title -->
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter post title" required>
            </div>
            <div>
                <!-- Textarea for post content -->
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="5" placeholder="Write your content here..." required></textarea>
            </div>
            <div>
                <!-- Dropdown to select a module -->
                <label for="module_id">Module</label>
                <select id="module_id" name="module_id" required>
                    <option value="" disabled selected>Select a module</option>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>">
                            <?php echo htmlspecialchars($module['module_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <!-- File upload for media -->
                <label for="file">Upload Image or Video</label>
                <input type="file" id="file" name="file" accept="image/*,video/*">
            </div>
            <!-- Submit button to create the post -->
            <button type="submit">Create Post</button>
        </form>
        <!-- Link to navigate back to the posts list -->
        <a href="view_posts.php" class="back-link">Back to Posts</a>
    </div>
</body>
</html>
