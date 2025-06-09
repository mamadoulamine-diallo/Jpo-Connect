
<?php
require_once '../classes/Comment.php';

header('Content-Type: application/json');

$comment = new Comment();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  if (isset($_GET['action']) && $_GET['action'] === 'get_pending') {
    $jpo_id = $_GET['jpo_id'] ?? null;
    $data = $comment->getPending($jpo_id);
    echo json_encode($data);
  } else {
    $jpo_id = $_GET['jpo_id'] ?? 0;
    if ($jpo_id <= 0) {
      http_response_code(400);
      echo json_encode(['error' => 'JPO ID is required']);
      exit;
    }
    $data = $comment->getAll($jpo_id);
    echo json_encode($data);
  }
} elseif ($method === 'POST') {
  $raw_input = file_get_contents('php://input');
  $raw_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
  $input = json_decode($raw_input, true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'create') {
    $result = $comment->create(
      $input['jpo_id'] ?? 0,
      $input['visitor_id'] ?? 0,
      $input['comment'] ?? ''
    );
    echo json_encode($result);
  } elseif (isset($input['action']) && $input['action'] === 'moderate') {
    $result = $comment->moderate(
      $input['comment_id'] ?? 0,
      $input['approve'] ?? false
    );
    echo json_encode($result);
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action not specified']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
}
?>