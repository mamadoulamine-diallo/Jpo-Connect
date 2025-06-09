
<?php
require_once '../classes/Inscription.php';

header('Content-Type: application/json');

$inscription = new Inscription();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $inscription->getAll();
  echo json_encode($data);
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
    $result = $inscription->create(
      $input['jpo_id'] ?? 0,
      $input['visitor_id'] ?? 0
    );
    echo json_encode($result);
  } elseif (isset($input['action']) && $input['action'] === 'delete') {
    $result = $inscription->delete(
      $input['jpo_id'] ?? 0,
      $input['visitor_id'] ?? 0
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