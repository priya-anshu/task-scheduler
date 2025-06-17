<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Must be POST & logged in
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Bad request']);
    exit;
}

$userId     = $_SESSION['user_id'];
$title      = trim($_POST['title'] ?? '');
$desc       = trim($_POST['description'] ?? '');
$due_date   = $_POST['due_date'] ?? '';
$priority   = $_POST['priority'] ?? 'medium';
$status     = $_POST['status'] ?? 'pending';

if (!$title || !$due_date) {
    echo json_encode(['success'=>false,'message'=>'Title and Due Date required']);
    exit;
}

$stmt = $conn->prepare("
  INSERT INTO tasks (user_id, title, description, due_date, priority, status) 
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('isssss', $userId, $title, $desc, $due_date, $priority, $status);
if ($stmt->execute()) {
    echo json_encode(['success'=>true,'id'=>$stmt->insert_id]);
} else {
    echo json_encode(['success'=>false,'message'=>'DB insert error']);
}
