<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(400);
    echo json_encode(['success'=>false]);
    exit;
}

$userId = $_SESSION['user_id'];
$taskId = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("
  DELETE FROM tasks 
  WHERE id = ? AND user_id = ?
");
$stmt->bind_param('ii', $taskId, $userId);
$stmt->execute();

echo json_encode(['success'=>$stmt->affected_rows > 0]);
