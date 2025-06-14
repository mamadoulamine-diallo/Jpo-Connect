<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Origine spécifique
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Supprime GET, DELETE si non utilisés
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Autorise les credentials
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

session_start();

require_once __DIR__ . '/../class/User.php';

$user = new User();
$response = ['success' => false];

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data && isset($data['username']) && isset($data['password'])) {
      $result = $user->login($data['username'], $data['password']);
      $response = is_array($result) ? $result : ['success' => false, 'error' => 'Erreur inattendue'];
    } else {
      $response = ['success' => false, 'error' => 'Données invalides'];
    }
  } else {
    $response = ['success' => false, 'error' => 'Méthode non supportée'];
  }
} catch (Exception $e) {
  $response = ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()];
}

echo json_encode($response);
