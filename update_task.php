<?php
session_start();
require 'config.php';

if (isset($_POST['task_id']) && isset($_POST['completed'])) {
    $task_id = $_POST['task_id'];
    $completed = $_POST['completed'];

    $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $completed, $task_id);
    if ($stmt->execute()) {
        echo "Task updated successfully";
    } else {
        echo "Error updating task";
    }
}
?>
