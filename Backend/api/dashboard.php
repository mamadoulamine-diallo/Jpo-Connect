
<?php
require_once '../classes/Dashboard.php';

header('Content-Type: application/json; charset=utf-8');

$dashboard = new Dashboard();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $dashboard->getData();
  echo json_encode($data);
} elseif ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'delete_jpo') {
    $result = $dashboard->deleteJpo($input['jpo_id'] ?? 0);
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