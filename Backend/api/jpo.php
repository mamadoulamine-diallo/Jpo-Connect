<?php
require_once '../classes/JPO.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$jpo = new JPO();

if ($method === 'GET') {
  $city = $_GET['city'] ?? '';
  $date = $_GET['date'] ?? '';
  echo json_encode($jpo->getAll($city, $date));
} elseif ($method === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  if (!isset($data['site_id'], $data['admin_id'], $data['date_jpo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
  }
  $result = $jpo->create($data['site_id'], $data['admin_id'], $data['date_jpo']);
  if (isset($result['error'])) {
    http_response_code(403);
    echo json_encode($result);
  } else {
    echo json_encode($result);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
}
