<?php
require_once '../classes/Inscription.php';

// Set CORS headers
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$inscription = new Inscription();
$method = $_SERVER['REQUEST_METHOD'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        if (!isset($input['action'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Action non spécifiée']);
            exit;
        }

        if ($input['action'] === 'create') {
            if (!isset($input['visitor_id']) || !isset($input['jpo_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Données manquantes']);
                exit;
            }

            $result = $inscription->create(
                $input['visitor_id'],
                $input['jpo_id']
            );

            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Action invalide']);
        }
        break;

    case 'GET':
        $visitor_id = isset($_GET['visitor_id']) ? $_GET['visitor_id'] : null;
        $jpo_id = isset($_GET['jpo_id']) ? $_GET['jpo_id'] : null;
        
        $result = $inscription->getAll($visitor_id, $jpo_id);
        echo json_encode($result);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}