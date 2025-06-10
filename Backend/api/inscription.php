
<?php
require_once '../classes/Inscription.php';

// Set CORS headers
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($method === 'GET') {
  $visitor_id = $_GET['visitor_id'] ?? null;
  $jpo_id = $_GET['jpo_id'] ?? null;
  $data = $inscription->getAll($visitor_id, $jpo_id);
  echo json_encode($data);
} elseif ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'create') {
    $result = $inscription->create(
      $input['visitor_id'] ?? 0,
      $input['jpo_id'] ?? 0
    );
    echo json_encode($result);
  } elseif (isset($input['action']) && $input['action'] === 'delete') {
    $result = $inscription->delete($input['id_inscription'] ?? 0);
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