<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

session_start();

require_once __DIR__ . '/../class/JPO.php';

$jpo = new JPO();
$method = $_SERVER['REQUEST_METHOD'];
$user_role = $_SESSION['role'] ?? 'guest';
$response = ['success' => false];

try {
  switch ($method) {
    case 'GET':
      $data = json_decode(file_get_contents('php://input'), true);
      $result = $jpo->getAll(); // Utilise getAll au lieu de get
      $response = is_array($result) ? ['success' => true, 'data' => $result] : ['success' => false, 'error' => $result['error'] ?? 'Échec de la récupération'];
      break;

    case 'POST':
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['title']) && isset($data['date']) && isset($data['location']) && isset($data['capacity']) && isset($data['description'])) {
        $result = $jpo->add($data['title'], $data['date'], $data['location'], $data['capacity'], $data['description'], $user_role);
        $response = is_array($result) ? $result : ['success' => false, 'error' => 'Réponse inattendue'];
      } else {
        $response = ['success' => false, 'error' => 'Données invalides'];
      }
      break;

    case 'PUT':
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['id']) && isset($data['title']) && isset($data['date']) && isset($data['location']) && isset($data['capacity']) && isset($data['description'])) {
        $result = $jpo->update($data['id'], $data['title'], $data['date'], $data['location'], $data['capacity'], $data['description'], $user_role);
        $response = is_array($result) ? $result : ['success' => false, 'error' => 'Réponse inattendue'];
      } else {
        $response = ['success' => false, 'error' => 'Données invalides'];
      }
      break;

    case 'DELETE':
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['id'])) {
        $result = $jpo->delete($data['id'], $user_role);
        $response = is_array($result) ? $result : ['success' => false, 'error' => 'Réponse inattendue'];
      } else {
        $response = ['success' => false, 'error' => 'Données invalides'];
      }
      break;

    default:
      $response = ['success' => false, 'error' => 'Méthode non supportée'];
      break;
  }
} catch (Exception $e) {
  $response = ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()];
}

echo json_encode($response);
