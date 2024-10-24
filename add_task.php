<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'], $_POST['list_id'])) {
    $user_id = $_SESSION['user_id'];
    $task_name = $_POST['task_name'];
    $list_id = $_POST['list_id'];

    // Insert the new task into the tasks table
    $stmt = $conn->prepare("INSERT INTO tasks (list_id, task_name, completed) VALUES (?, ?, 0)");
    $stmt->bind_param("is", $list_id, $task_name);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: Could not add the task.";
    }
}
?>
