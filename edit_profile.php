<?php
session_start();
require 'config.php'; // Koneksi ke database

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle profile update
    $new_username = htmlspecialchars($_POST['username']);
    $new_email = htmlspecialchars($_POST['email']);
    $new_password = htmlspecialchars($_POST['password']);
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update username, email, and password
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);

    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['username'] = $new_username;
        $_SESSION['email'] = $new_email;
        header("Location: dashboard.php"); // Redirect back to dashboard after update
        exit();
    } else {
        $error_message = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
        .profile-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-form input[type="text"],
        .profile-form input[type="email"],
        .profile-form input[type="password"] {
            margin-bottom: 15px;
        }
        .btn {
            width: 100%;
        }
        .back-to-dashboard {
            margin-top: 20px;
            text-align: center;
        }
        .back-to-dashboard a {
            text-decoration: none;
        }
        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="profile-container">
        <h3>Edit Your Profile</h3>
    </div>
    <form action="edit_profile.php" method="POST" class="profile-form">
        <div class="form-group">
            <label for="username">Name</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <?php if (isset($error_message)): ?>
        <div class="error">
            <?= htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
    <div class="back-to-dashboard">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
