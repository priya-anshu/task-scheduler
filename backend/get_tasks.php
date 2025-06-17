<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
  SELECT id, title, description, due_date, priority, status 
  FROM tasks 
  WHERE user_id = ? 
  ORDER BY due_date ASC, created_at DESC
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
echo json_encode($tasks);
