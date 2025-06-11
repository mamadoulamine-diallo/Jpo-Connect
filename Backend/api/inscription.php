
<?php
require_once '../classes/Inscription.php';

header('Content-Type: application/json; charset=utf-8');

$inscription = new Inscription();
$method = $_SERVER['REQUEST_METHOD'];

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