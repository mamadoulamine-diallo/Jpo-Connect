<?php
require_once '../classes/Admin.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$admin = new Admin();

if ($method === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  if (isset($data['action']) && $data['action'] === 'login') {
    $result = $admin->login($data['email'], $data['password']);
    if ($result) {
      echo json_encode($result);
    } else {
      http_response_code(401);
      echo json_encode(['error' => 'E-mail ou mot de passe incorrect']);
    }
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action non spécifiée']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
}
