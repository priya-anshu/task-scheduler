<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

// Grab & sanitize inputs
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

$errors = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if ($password === '') {
    $errors[] = 'Password cannot be blank.';
}
if ($errors) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

// Lookup user
$stmt = $conn->prepare('SELECT id, full_name, password FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'No account found with that email.'
    ]);
    exit;
}

$stmt->bind_result($id, $fullName, $hash);
$stmt->fetch();

// Verify password
if (!password_verify($password, $hash)) {
    echo json_encode([
        'success' => false,
        'message' => 'Incorrect password.'
    ]);
    exit;
}

// Success: set session + optional “remember me” cookie
$_SESSION['user_id']   = $id;
$_SESSION['user_name'] = $fullName;
if ($remember) {
    setcookie('remember_me', $id, time() + 86400 * 30, '/');
}

// Send back JSON success + redirect URL
echo json_encode([
    'success'  => true,
    'redirect' => '../ts/index.php'
]);
exit;
