
<?php
require_once '../classes/Dashboard.php';

header('Content-Type: application/json; charset=utf-8');

$dashboard = new Dashboard();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $dashboard->getData();
  echo json_encode($data);
} elseif ($method === 'POST') {
  $raw_input = file_get_contents('php://input');
  $raw_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
  $input = json_decode($raw_input, true);
  if (is_null($input) || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide : ' . json_last_error_msg()]);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'delete_jpo') {
    $result = $dashboard->deleteJpo($input['jpo_id'] ?? 0);
    echo json_encode($result);
  } elseif (isset($input['action']) && $input['action'] === 'update_jpo') {
    $result = $dashboard->updateJpo(
      $input['jpo_id'] ?? 0,
      $input['title'] ?? '',
      $input['date_jpo'] ?? '',
      $input['site_id'] ?? 0,
      $input['description'] ?? null
    );
    echo json_encode($result);
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action non spécifiée']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
}
?>