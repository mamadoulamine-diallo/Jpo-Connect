
<?php
require_once '../classes/Comment.php';

header('Content-Type: application/json; charset=utf-8');

$comment = new Comment();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $comment->getPending();
  echo json_encode($data);
} elseif ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
  }
  if (isset($input['action'])) {
    if ($input['action'] === 'create') {
      $result = $comment->create(
        $input['visitor_id'] ?? 0,
        $input['jpo_id'] ?? 0,
        $input['content'] ?? ''
      );
      echo json_encode($result);
    } elseif ($input['action'] === 'moderate') {
      $result = $comment->moderate(
        $input['comment_id'] ?? 0,
        $input['is_approved'] ?? 0
      );
      echo json_encode($result);
    } elseif ($input['action'] === 'reply') {
      $result = $comment->reply(
        $input['comment_id'] ?? 0,
        $input['content'] ?? ''
      );
      echo json_encode($result);
    } else {
      http_response_code(400);
      echo json_encode(['error' => 'Action non valide']);
    }
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action non spécifiée']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
}
?>