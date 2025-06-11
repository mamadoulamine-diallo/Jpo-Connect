
<?php
require_once '../classes/Jpo.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$jpo = new Jpo();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $city = $_GET['city'] ?? null;
  $date = $_GET['date'] ?? null;
  $data = $jpo->getAll($city, $date);
  echo json_encode($data);
} elseif ($method === 'POST') {
  $raw_input = file_get_contents('php://input');
  $raw_input = mb_convert_encoding($raw_input, 'UTF-8', 'auto');
  $input = json_decode($raw_input, true);
  if (is_null($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit;
  }
  if (isset($input['action']) && $input['action'] === 'create') {
    $result = $jpo->create(
      $input['title'] ?? '',
      $input['date_jpo'] ?? '',
      $input['site_id'] ?? 0,
      $input['description'] ?? null
    );
    echo json_encode($result);
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Action not specified']);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
}

?>