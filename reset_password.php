<?php
require 'config.php';
session_start();

$notification = ""; // Variabel untuk menyimpan pesan notifikasi
$notification_type = ""; // Variabel untuk menyimpan jenis notifikasi (success atau error)

// Periksa apakah token ada di URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    // Jika token tidak ada, arahkan pengguna atau tampilkan pesan kesalahan
    $notification = "Token tidak ditemukan. Silakan coba lagi.";
    $notification_type = "error";
}

// Proses form hanya jika token valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($token)) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT); // Enkripsi password baru

    // Verifikasi token di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $new_password, $token);
        
        if ($stmt->execute()) {
            $notification = "Password berhasil diubah.";
            $notification_type = "success"; // Set notifikasi sebagai sukses
        } else {
            $notification = "Gagal mengubah password: " . $stmt->error;
            $notification_type = "error"; // Set notifikasi sebagai error
        }
    } else {
        $notification = "Token tidak valid atau telah kadaluarsa.";
        $notification_type = "error"; // Set notifikasi sebagai error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Style notifikasi */
        .notification {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }
        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Style form seperti sebelumnya */
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <!-- Tampilkan notifikasi jika ada -->
        <?php if (!empty($notification)) : ?>
            <div class="notification <?php echo $notification_type; ?>">
                <?php echo $notification; ?>
            </div>
        <?php endif; ?>

        <!-- Form reset password hanya jika token tersedia -->
        <?php if (isset($token)) : ?>
            <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
