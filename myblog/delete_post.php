<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require 'db.php';

// Check if the post ID is set
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Delete the post from the database
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $post_id]);

    // Redirect back to view posts page
    header('Location: view_posts.php');
    exit();
}
