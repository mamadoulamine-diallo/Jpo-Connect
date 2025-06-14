<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Remplace * par l'origine spécifique pour des raisons de sécurité
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Permet l'envoi de cookies
header('Access-Control-Max-Age: 86400'); // Cache le préflight pour 24h

// Gère la requête OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204); // No content
  exit(0);
}

session_start();

require_once __DIR__ . '/../class/Inscription.php';
require_once __DIR__ . '/../class/User.php';

$user = new User();
$inscription = new Inscription();
$method = $_SERVER['REQUEST_METHOD'];
$user_role = $_SESSION['role'] ?? 'guest'; // Définit $user_role à partir de la session
$response = ['success' => false];

try {
  switch ($method) {
    case 'POST':
      // S'inscrire à une JPO (publique)
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['jpo_id']) && isset($data['user_name']) && isset($data['user_email'])) {
        $response = $inscription->register($data['jpo_id'], $data['user_name'], $data['user_email']);
      } else {
        $response = ['error' => 'Données invalides'];
      }
      break;

    case 'DELETE':
      // Se désinscrire d'une JPO (publique)
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['jpo_id']) && isset($data['user_email'])) {
        $response = $inscription->unregister($data['jpo_id'], $data['user_email']);
      } else {
        $response = ['error' => 'Données invalides'];
      }
      break;

    case 'GET':
      // Lister les inscriptions pour une JPO (protégée)
      $data = json_decode(file_get_contents('php://input'), true);
      if ($data && isset($data['jpo_id'])) {
        $response = $inscription->getInscriptionsByJPO($data['jpo_id'], $user_role);
      } else {
        $response = ['error' => 'Données invalides'];
      }
      break;

    default:
      $response = ['error' => 'Méthode non supportée'];
      break;
  }
} catch (Exception $e) {
  $response = ['error' => 'Erreur serveur: ' . $e->getMessage()];
}

echo json_encode($response);
