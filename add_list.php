<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['list_name'])) {
    $user_id = $_SESSION['user_id'];
    $list_name = $_POST['list_name'];

    // Insert the new to-do list into the database
    $stmt = $conn->prepare("INSERT INTO lists (user_id, list_name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $list_name);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: Could not add the list.";
    }
}
?>
