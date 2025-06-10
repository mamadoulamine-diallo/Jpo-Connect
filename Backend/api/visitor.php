<?php
require_once '../classes/Visitor.php';

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$visitor = new Visitor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Action not specified']);
        exit;
    }

    if ($input['action'] === 'create') {
        $result = $visitor->create(
            $input['nom'],
            $input['prenom'],
            $input['email'],
            $input['jpo_id'] ?? null
        );
        echo json_encode($result);
    }
}

?>