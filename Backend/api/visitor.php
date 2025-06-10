```php
<?php
require_once '../classes/Visitor.php';

header('Content-Type: application/json; charset=utf-8');

$visitor = new Visitor();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalide']);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'login') {
    $result = $visitor->login($input['email'] ?? '');
    if ($result) {
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }
      // Débogage : enregistrer la session visiteur
      file_put_contents(__DIR__ . '/debug_visitor_session.txt', print_r($_SESSION['visitor'], true));
      echo json_encode($result);
    } else {
      http_response_code(401);
      echo json_encode(['error' => 'Email invalide']);
    }
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action non spécifiée']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
}
?>