<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM modules WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo "Module deleted successfully!";
        echo '
<br><a href="admin_dashboard.php">Back to Modules</a>';
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No module ID provided!";
}
?>