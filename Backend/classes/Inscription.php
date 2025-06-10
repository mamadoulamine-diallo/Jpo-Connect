
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
    if (!isset($_SESSION['visitor'])) {
      http_response_code(401);
      return ['error' => 'Utilisateur non connecté'];
    }
    if ($_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Impossible d’inscrire un autre utilisateur'];
    }

    $stmt = $this->pdo->prepare(
      "SELECT capacity, (SELECT COUNT(*) FROM inscriptions WHERE jpo_fk = :jpo_id) AS inscrits
             FROM jpo WHERE id_jpo = :jpo_id"
    );
    $stmt->execute([':jpo_id' => $jpo_id]);
    $jpo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$jpo) {
      http_response_code(400);
      return ['error' => 'JPO invalide'];
    }
    if ($jpo['capacity'] && $jpo['inscrits'] >= $jpo['capacity']) {
      http_response_code(400);
      return ['error' => 'Capacité maximale atteinte'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO inscriptions (visitor_fk, jpo_fk) VALUES (:visitor_id, :jpo_id)"
      );
      $stmt->execute([':visitor_id' => $visitor_id, ':jpo_id' => $jpo_id]);
      return ['message' => 'Inscription réussie'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function getAll($visitor_id = null, $jpo_id = null)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Utilisateur non connecté'];
    }

    $query = "SELECT i.id_inscription, i.visitor_fk, i.jpo_fk, j.title, j.date_jpo, s.city
                  FROM inscriptions i
                  JOIN jpo j ON i.jpo_fk = j.id_jpo
                  JOIN site s ON j.site_fk = s.id_site WHERE 1=1";
    $params = [];

    if ($visitor_id) {
      $query .= " AND i.visitor_fk = :visitor_id";
      $params[':visitor_id'] = $visitor_id;
      if (isset($_SESSION['visitor']) && $_SESSION['visitor']['id'] !== $visitor_id) {
        http_response_code(403);
        return ['error' => 'Accès non autorisé aux inscriptions d’un autre utilisateur'];
      }
    }
    if ($jpo_id) {
      $query .= " AND i.jpo_fk = :jpo_id";
      $params[':jpo_id'] = $jpo_id;
    }

    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function delete($id_inscription)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Utilisateur non connecté'];
    }

    $stmt = $this->pdo->prepare(
      "SELECT id_inscription, visitor_fk FROM inscriptions WHERE id_inscription = :id_inscription"
    );
    $stmt->execute([':id_inscription' => $id_inscription]);
    $inscription = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$inscription) {
      http_response_code(404);
      return ['error' => 'Inscription introuvable'];
    }

    if (isset($_SESSION['visitor']) && $_SESSION['visitor']['id'] !== $inscription['visitor_fk']) {
      http_response_code(403);
      return ['error' => 'Impossible de supprimer l’inscription d’un autre utilisateur'];
    }

    try {
      $stmt = $this->pdo->prepare("DELETE FROM inscriptions WHERE id_inscription = :id_inscription");
      $stmt->execute([':id_inscription' => $id_inscription]);
      return ['message' => 'Désinscription réussie'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }
}

?>