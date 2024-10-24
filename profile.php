<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user data from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .profile-container {
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .profile-container h2 {
            text-align: center;
        }

        .profile-container p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .profile-container img {
            display: block;
            margin: auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">To-Do App</a>
        <div class="ml-auto">
            <a href="edit_profile.php" class="btn btn-outline-info mr-2">Edit Profile</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-container">
            <h2><?= htmlspecialchars($user['username']); ?>'s Profile</h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        </div>
    </div>
</body>
</html>
