<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        echo "Task deleted successfully";
    } else {
        echo "Error deleting task";
    }
}
?>
