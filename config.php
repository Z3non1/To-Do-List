<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Ganti dengan username database-mu
$password = ""; // Ganti dengan password database-mu
$dbname = "todo_app"; // Nama database yang sudah dibuat

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
