<?php
require_once __DIR__ . '/../config/database.php';

class Stats
{
  private $db;

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getConnection();
  }

  // Obtenir les statistiques pour une JPO
  public function getByJPO($jpo_id, $user_role)
  {
    if ($user_role !== 'director' && $user_role !== 'manager' && $user_role !== 'employee') {
      return ['error' => 'Permission refusée'];
    }

    // Nombre d'inscrits
    $query = "SELECT COUNT(*) as inscriptions FROM inscriptions WHERE jpo_id = :jpo_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':jpo_id', $jpo_id);
    $stmt->execute();
    $inscriptions = $stmt->fetch(PDO::FETCH_ASSOC)['inscriptions'];

    // Nombre de commentaires
    $query = "SELECT COUNT(*) as comments FROM comments WHERE jpo_id = :jpo_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':jpo_id', $jpo_id);
    $stmt->execute();
    $comments = $stmt->fetch(PDO::FETCH_ASSOC)['comments'];

    // Capacité de la JPO
    $query = "SELECT capacity FROM jpos WHERE id = :jpo_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':jpo_id', $jpo_id);
    $stmt->execute();
    $capacity = $stmt->fetch(PDO::FETCH_ASSOC)['capacity'];

    return [
      'inscriptions' => $inscriptions,
      'comments' => $comments,
      'capacity' => $capacity
    ];
  }
}
