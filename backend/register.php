<?php
// backend/register.php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// Sanitize & collect
$fullName       = trim($_POST['fullName'] ?? '');
$email          = trim($_POST['email'] ?? '');
$password       = $_POST['password'] ?? '';
$confirmPassword= $_POST['confirmPassword'] ?? '';
$terms          = isset($_POST['terms']);

$errors = [];
if ($fullName === '')                    $errors[] = 'Full name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (strlen($password) < 6)               $errors[] = 'Password must be at least 6 characters.';
if ($password !== $confirmPassword)      $errors[] = 'Passwords do not match.';
if (!$terms)                             $errors[] = 'You must agree to the Terms.';

if ($errors) {
  $_SESSION['flash_error'] = implode('<br>', $errors);
  header('Location: ../register.php');
  exit;
}

// Check for duplicate email
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  $_SESSION['flash_error'] = 'Email already in use.';
  header('Location: ../backend/register.php');
  exit;
}
$stmt->close();

// Insert new user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $fullName, $email, $hash);
if ($stmt->execute()) {
  $_SESSION['user_id']   = $stmt->insert_id;
  $_SESSION['user_name'] = $fullName;
  header('Location: ../index.php');
} else {
  $_SESSION['flash_error'] = 'Registration failed; please try again.';
  header('Location: ../register.php');
}
$stmt->close();
$conn->close();
