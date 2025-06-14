<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../class/Stats.php';
require_once __DIR__ . '/../class/User.php';

$user = new User();
$stats = new Stats();
$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'GET') {
  $data = json_decode(file_get_contents('php://input'), true);
  echo json_encode($stats->getByJPO(
    $data['jpo_id'],
    $user_role
  ));
} else {
  echo json_encode(['error' => 'Méthode non supportée']);
}
