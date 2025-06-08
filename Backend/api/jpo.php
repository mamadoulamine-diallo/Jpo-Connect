<?php
require_once '../classes/Jpo.php';

header('Content-Type: application/json');

$jpo = new JPO();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $jpo->getAll();
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
    $result = $jpo->create(
      $input['title'] ?? '',
      $input['date_jpo'] ?? '',
      $input['site_id'] ?? '',
      $input['description'] ?? '',
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
