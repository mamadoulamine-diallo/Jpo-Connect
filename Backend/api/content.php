<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0); // Gère la preflight request
}

session_start();

require_once __DIR__ . '/../class/Content.php';
require_once __DIR__ . '/../class/User.php';

$user = new User();
$content = new Content();
$method = $_SERVER['REQUEST_METHOD'];
$user_role = $_SESSION['role'] ?? 'guest'; // Récupère le rôle depuis la session

switch ($method) {
  case 'GET':
    $key = $_GET['key'] ?? null; // Utilise $_GET pour les paramètres GET
    $response = ['success' => false];
    if ($key) {
      $value = $content->get($key);
      if ($value !== null) {
        $response = ['success' => true, 'value' => $value];
      } else {
        $response['error'] = 'Contenu non trouvé';
      }
    } else {
      $response['error'] = 'Clé manquante';
    }
    echo json_encode($response);
    break;

  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    $response = ['success' => false];
    if ($data && isset($data['key']) && isset($data['value'])) {
      $result = $content->update($data['key'], $data['value'], $user_role);
      $response = ['success' => $result === true, 'message' => $result ? 'Mis à jour' : 'Échec'];
    } else {
      $response['error'] = 'Données invalides';
    }
    echo json_encode($response);
    break;

  default:
    echo json_encode(['error' => 'Méthode non supportée']);
    break;
}
