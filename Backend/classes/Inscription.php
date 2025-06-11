<?php

require_once __DIR__ . '/../config/Database.php';

class Inscription
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = (new Database())->getConnection();
    }

    public function create($visitor_id, $jpo_id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérification de l'authentification
        if (!isset($_SESSION['visitor'])) {
            http_response_code(401);
            return ['success' => false, 'error' => 'Utilisateur non connecté'];
        }

        // Vérification de l'identité
        if ($_SESSION['visitor']['id'] !== $visitor_id) {
            http_response_code(403);
            return ['success' => false, 'error' => 'Impossible d\'inscrire un autre utilisateur'];
        }

        try {
            // Vérification de l'inscription existante
            $checkStmt = $this->pdo->prepare(
                "SELECT id_inscription FROM inscriptions 
                WHERE visitor_fk = :visitor_id AND jpo_fk = :jpo_id"
            );
            $checkStmt->execute([
                ':visitor_id' => $visitor_id,
                ':jpo_id' => $jpo_id
            ]);
            
            if ($checkStmt->fetch()) {
                return [
                    'success' => false,
                    'error' => 'Vous êtes déjà inscrit à cette JPO'
                ];
            }

            // Vérification de la capacité
            $stmt = $this->pdo->prepare(
                "SELECT capacity, 
                (SELECT COUNT(*) FROM inscriptions WHERE jpo_fk = :jpo_id) AS inscrits
                FROM jpo WHERE id_jpo = :jpo_id"
            );
            $stmt->execute([':jpo_id' => $jpo_id]);
            $jpo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$jpo) {
                http_response_code(400);
                return ['success' => false, 'error' => 'JPO invalide'];
            }

            if ($jpo['capacity'] && $jpo['inscrits'] >= $jpo['capacity']) {
                http_response_code(400);
                return ['success' => false, 'error' => 'Capacité maximale atteinte'];
            }

            // Création de l'inscription
            $insertStmt = $this->pdo->prepare(
                "INSERT INTO inscriptions (visitor_fk, jpo_fk, created_at) 
                VALUES (:visitor_id, :jpo_id, NOW())"
            );
            $insertStmt->execute([
                ':visitor_id' => $visitor_id,
                ':jpo_id' => $jpo_id
            ]);

            return [
                'success' => true,
                'message' => 'Inscription réussie',
                'id' => $this->pdo->lastInsertId()
            ];

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Erreur lors de l\'inscription'
            ];
        }
    }

    public function getAll($visitor_id = null, $jpo_id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
            http_response_code(401);
            return ['success' => false, 'error' => 'Utilisateur non connecté'];
        }

        try {
            $query = "SELECT i.id_inscription, i.visitor_fk, i.jpo_fk, 
                        j.title, j.date_jpo, s.city, i.created_at
                     FROM inscriptions i
                     JOIN jpo j ON i.jpo_fk = j.id_jpo
                     JOIN site s ON j.site_fk = s.id_site 
                     WHERE 1=1";
            $params = [];

            if ($visitor_id) {
                if (isset($_SESSION['visitor']) && $_SESSION['visitor']['id'] !== $visitor_id) {
                    http_response_code(403);
                    return ['success' => false, 'error' => 'Accès non autorisé'];
                }
                $query .= " AND i.visitor_fk = :visitor_id";
                $params[':visitor_id'] = $visitor_id;
            }

            if ($jpo_id) {
                $query .= " AND i.jpo_fk = :jpo_id";
                $params[':jpo_id'] = $jpo_id;
            }

            $query .= " ORDER BY i.created_at DESC";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $inscriptions
            ];

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération des inscriptions'
            ];
        }
    }
//a implementer encore
    public function delete($id_inscription)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
            http_response_code(401);
            return ['success' => false, 'error' => 'Utilisateur non connecté'];
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_inscription, visitor_fk 
                FROM inscriptions 
                WHERE id_inscription = :id_inscription"
            );
            $stmt->execute([':id_inscription' => $id_inscription]);
            $inscription = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$inscription) {
                http_response_code(404);
                return ['success' => false, 'error' => 'Inscription introuvable'];
            }

            if (isset($_SESSION['visitor']) && 
                $_SESSION['visitor']['id'] !== $inscription['visitor_fk']) {
                http_response_code(403);
                return [
                    'success' => false, 
                    'error' => 'Impossible de supprimer l\'inscription d\'un autre utilisateur'
                ];
            }

            $stmt = $this->pdo->prepare(
                "DELETE FROM inscriptions WHERE id_inscription = :id_inscription"
            );
            $stmt->execute([':id_inscription' => $id_inscription]);

            return [
                'success' => true,
                'message' => 'Désinscription réussie'
            ];

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'inscription'
            ];
        }
    }
}