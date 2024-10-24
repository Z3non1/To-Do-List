<?php
require 'config.php'; // Koneksi ke database
require 'vendor/autoload.php'; // Autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$message = '';
$message_class = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);

    // Cek apakah email terdaftar di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generate token untuk reset password
        $reset_token = bin2hex(random_bytes(16));
        $reset_token_expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token berlaku 1 jam

        // Simpan token di database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $reset_token_expires, $email);
        $stmt->execute();

        // Kirim email ke pengguna dengan link reset password
        $reset_link = "http://localhost/UTS_WEBPROG_LAB_/reset_password.php?token=" . $reset_token;
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Ganti dengan host SMTP Anda
        $mail->SMTPAuth = true;
        $mail->Username = 'reynardgeovanni@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'ezyp idmm rotq ehez'; // Ganti dengan password email Anda
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('todolist@gmail.com', 'To do List');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Request';
        $mail->Body    = "Hello, <br><br>Click the following link to reset your password: <a href='$reset_link'>$reset_link</a><br>This link will expire in 1 hour.";
        $mail->AltBody = "Copy and paste this link in your browser to reset your password: $reset_link";

        if ($mail->send()) {
            $message = "Reset password link has been sent to your email.";
            $message_class = 'success';
        } else {
            $message = "Failed to send reset password email. Please try again.";
            $message_class = 'error';
        }
    } else {
        $message = "Email not found.";
        $message_class = 'error';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
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
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($message): ?>
            <p class="message <?= $message_class; ?>"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php">
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
