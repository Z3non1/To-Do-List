<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

if (isset($_POST['list_id'])) {
    $list_id = $_POST['list_id'];

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM lists WHERE id = ?");
    $stmt->bind_param("i", $list_id);

    if ($stmt->execute()) {
        echo "List deleted successfully";
    } else {
        echo "Error deleting list";
    }
}
?>
