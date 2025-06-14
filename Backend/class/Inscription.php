<?php
require_once __DIR__ . '/../config/database.php';

class Inscription
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // S'inscrire à une JPO
    public function register($jpo_id, $user_name, $user_email)
    {
        // Vérifier si la JPO existe
        $query = "SELECT capacity, (SELECT COUNT(*) FROM inscriptions WHERE jpo_id = :jpo_id) as current_inscriptions FROM jpos WHERE id = :jpo_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
        $stmt->execute();
        $jpo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$jpo) {
            return ['error' => 'JPO introuvable'];
        }

        // Vérifier si la capacité est dépassée
        if ($jpo['current_inscriptions'] >= $jpo['capacity']) {
            return ['error' => 'Capacité maximale atteinte'];
        }

        // Vérifier si l'utilisateur est déjà inscrit
        $query = "SELECT id FROM inscriptions WHERE jpo_id = :jpo_id AND user_email = :user_email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['error' => 'Vous êtes déjà inscrit à cette JPO'];
        }

        // Ajouter l'inscription
        $query = "INSERT INTO inscriptions (jpo_id, user_name, user_email) VALUES (:jpo_id, :user_name, :user_email)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return ['success' => 'Inscription réussie'];
        } else {
            return ['error' => 'Erreur lors de l\'inscription'];
        }
    }

    // Se désinscrire d'une JPO
    public function unregister($jpo_id, $user_email)
    {
        $query = "DELETE FROM inscriptions WHERE jpo_id = :jpo_id AND user_email = :user_email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return ['success' => 'Désinscription réussie'];
        } else {
            return ['error' => 'Aucune inscription trouvée'];
        }
    }

    // Lister les inscriptions pour une JPO (pour le tableau de bord)
    public function getInscriptionsByJPO($jpo_id, $user_role)
    {
        if ($user_role !== 'director' && $user_role !== 'manager' && $user_role !== 'employee') {
            return ['error' => 'Permission refusée'];
        }

        $query = "SELECT * FROM inscriptions WHERE jpo_id = :jpo_id ORDER BY registered_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
