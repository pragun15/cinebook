<?php
// Database configuration
$host = "localhost";
$username = "root"; // Default XAMPP/WAMP username
$password = ""; // Default XAMPP/WAMP password
$database = "movie_booking";

// Create database connection using mysqli
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
