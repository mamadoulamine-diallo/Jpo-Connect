
<?php
// Masquer les warnings en production
error_reporting(E_ALL & ~E_WARNING);

require_once '../classes/Admin.php';

header('Content-Type: application/json; charset=utf-8');

$admin = new Admin();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $data = $admin->getAll();
  echo json_encode($data);
} elseif ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'login') {
    $result = $admin->login($input['email'] ?? '', $input['password'] ?? '');
    if ($result) {
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }
      echo json_encode($result);
    } else {
      http_response_code(401);
      echo json_encode(['error' => 'E-mail ou mot de passe incorrect']);
    }
  } elseif (isset($input['action']) && $input['action'] === 'update_role') {
    $result = $admin->updateRole(
      $input['admin_id'] ?? 0,
      $input['role'] ?? '',
      $input['permissions'] ?? []
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