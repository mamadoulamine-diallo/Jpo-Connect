<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../utils/Email.php';
require_once __DIR__ . '/../class/User.php';

$user = new User();
$email = new Email();
$method = $_SERVER['REQUEST_METHOD'];



if ($method === 'POST') {
  if ($user_role !== 'director' && $user_role !== 'manager') {
    echo json_encode(['error' => 'Permission refusée']);
    return;
  }

  $data = json_decode(file_get_contents('php://input'), true);
  echo json_encode($email->sendReminder(
    $data['jpo_id'],
    $data['user_email'],
    $data['jpo_title'],
    $data['jpo_date'],
    $data['jpo_location']
  ));
} else {
  echo json_encode(['error' => 'Méthode non supportée']);
}
