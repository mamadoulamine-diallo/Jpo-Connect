<?php
require_once __DIR__ . '/../config/database.php';

class Comment
{
  private $db;

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getConnection();
  }

  // Ajouter un commentaire
  public function add($jpo_id, $user_name, $comment)
  {
    try {
      // Vérifier si la JPO existe
      $query = "SELECT id FROM jpos WHERE id = :jpo_id";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() === 0) {
        return ['error' => 'JPO introuvable'];
      }

      // Ajouter le commentaire (non approuvé par défaut)
      $query = "INSERT INTO comments (jpo_id, user_name, comment, is_approved) VALUES (:jpo_id, :user_name, :comment, FALSE)";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
      $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
      $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

      if ($stmt->execute()) {
        return ['success' => 'Commentaire soumis, en attente de modération'];
      } else {
        return ['error' => 'Erreur lors de l\'ajout du commentaire'];
      }
    } catch (PDOException $e) {
      return ['error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }

  // Approuver un commentaire
  public function approve($comment_id, $user_role)
  {
    try {
      if ($user_role !== 'director' && $user_role !== 'manager') {
        return ['error' => 'Permission refusée'];
      }

      $query = "UPDATE comments SET is_approved = TRUE WHERE id = :comment_id";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

      if ($stmt->execute()) {
        return ['success' => 'Commentaire approuvé'];
      } else {
        return ['error' => 'Erreur lors de l\'approbation'];
      }
    } catch (PDOException $e) {
      return ['error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }

  // Supprimer un commentaire
  public function delete($comment_id, $user_role)
  {
    try {
      if ($user_role !== 'director' && $user_role !== 'manager') {
        return ['error' => 'Permission refusée'];
      }

      $query = "DELETE FROM comments WHERE id = :comment_id";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

      if ($stmt->execute()) {
        return ['success' => 'Commentaire supprimé'];
      } else {
        return ['error' => 'Erreur lors de la suppression'];
      }
    } catch (PDOException $e) {
      return ['error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }

  // Lister les commentaires pour une JPO
  public function getByJPO($jpo_id)
  {
    try {
      $query = "SELECT * FROM comments WHERE jpo_id = :jpo_id AND is_approved = TRUE ORDER BY created_at DESC";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':jpo_id', $jpo_id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return ['error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }

  // Lister tous les commentaires (pour modération)
  public function getAll($user_role)
  {
    try {
      if ($user_role !== 'director' && $user_role !== 'manager') {
        return ['error' => 'Permission refusée'];
      }

      $query = "SELECT * FROM comments ORDER BY created_at DESC";
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return ['error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }
}
