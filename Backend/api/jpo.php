
<?php
require_once '../classes/Jpo.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$jpo = new Jpo();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $city = $_GET['city'] ?? null;
  $date = $_GET['date'] ?? null;
  $start_date = $_GET['start_date'] ?? null;
  $end_date = $_GET['end_date'] ?? null;
  $keyword = $_GET['keyword'] ?? null;
  $data = $jpo->getAll($city, $date, $start_date, $end_date, $keyword);
  echo json_encode($data);
} elseif ($method === 'POST') {
  $raw_input = file_get_contents('php://input');
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