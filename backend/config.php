<?php
// backend/config.php
// Database configuration
$db_host = 'localhost';
$db_user = 'root'; // Change as needed
$db_pass = '';
$db_name = 'task_scheduler';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Always use UTF8
$conn->set_charset('utf8mb4');
