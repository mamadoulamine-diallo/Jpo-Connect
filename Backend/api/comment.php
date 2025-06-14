<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // Origine spécifique
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Autorise les credentials
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0); // Gère la preflight request
}

session_start();

require_once __DIR__ . '/../class/Comment.php';

$comment = new Comment();
$method = $_SERVER['REQUEST_METHOD'];
$user_role = $_SESSION['role'] ?? 'guest'; // Utilise la session au lieu de HTTP_ROLE

$response = ['success' => false];

switch ($method) {
  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data && isset($data['jpo_id']) && isset($data['user_name']) && isset($data['comment'])) {
      $result = $comment->add($data['jpo_id'], $data['user_name'], $data['comment']);
      $response = is_array($result) ? $result : ['success' => false, 'error' => 'Erreur inattendue'];
    } else {
      $response = ['success' => false, 'error' => 'Données invalides'];
    }
    break;

  case 'PUT':
    // Approuver un commentaire
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data && isset($data['comment_id'])) {
      $result = $comment->approve($data['comment_id'], $user_role);
      $response = is_array($result) ? $result : ($result === true ? ['success' => true, 'message' => 'Commentaire approuvé'] : ['success' => false, 'error' => 'Échec de l\'approbation']);
    } else {
      $response = ['success' => false, 'error' => 'Données invalides'];
    }
    break;

  case 'DELETE':
    // Supprimer un commentaire
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data && isset($data['comment_id'])) {
      $result = $comment->delete($data['comment_id'], $user_role);
      $response = is_array($result) ? $result : ($result === true ? ['success' => true, 'message' => 'Commentaire supprimé'] : ['success' => false, 'error' => 'Échec de la suppression']);
    } else {
      $response = ['success' => false, 'error' => 'Données invalides'];
    }
    break;

  case 'GET':
    // Lister les commentaires
    $jpo_id = $_GET['jpo_id'] ?? null;
    if ($jpo_id) {
      $result = $comment->getByJPO($jpo_id);
      $response = is_array($result) ? ['success' => true, 'data' => $result] : ['success' => false, 'error' => $result['error'] ?? 'Erreur lors de la récupération'];
    } else {
      $result = $comment->getAll($user_role);
      $response = is_array($result) ? ['success' => true, 'data' => $result] : ['success' => false, 'error' => $result['error'] ?? 'Permission refusée'];
    }
    break;

  default:
    $response = ['success' => false, 'error' => 'Méthode non supportée'];
    break;
}

echo json_encode($response);
