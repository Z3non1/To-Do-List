<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Simpan informasi user dalam session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Arahkan user ke halaman dashboard
        header("Location: dashboard.php");
    } else {
        $error = "Invalid email or password!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- CSS langsung di dalam HTML -->
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for the form */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        /* Input field styling */
        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Button styling */
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Error message styling */
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Link styling */
        p {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .link {
            color: #007bff; /* Warna biru seperti yang umum pada link */
            text-decoration: none; /* Menghilangkan garis bawah default */
        }

        .link:hover {
            text-decoration: underline; /* Menggarisbawahi ketika hover */
            color: #0056b3; /* Warna biru yang lebih gelap saat di-hover */
        }

        .link:focus {
            outline: none; /* Menghilangkan outline default saat fokus */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Login</h2>

            <!-- Error message -->
            <?php if (isset($error)) : ?>
                <p class="error"><?= $error; ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
            <p><a href="forgot_password.php" class="link">Forgot your password?</a></p>
        </div>
    </div>
</body>
</html>
