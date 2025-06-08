
<?php
require_once __DIR__ . '/../config/Database.php';

class Inscription
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  public function create($jpo_id, $visitor_id)
  {
    session_start();
    if (!isset($_SESSION['visitor'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    if ($_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Cannot register for another visitor'];
    }

    $stmt = $this->pdo->prepare("SELECT id_jpo FROM jpo WHERE id_jpo = :jpo_id");
    $stmt->execute([':jpo_id' => $jpo_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid JPO'];
    }

    $stmt = $this->pdo->prepare("SELECT id_visitors FROM visitors WHERE id_visitors = :visitor_id");
    $stmt->execute([':visitor_id' => $visitor_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid visitor'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO inscriptions (jpo_fk, visitor_fk) 
                 VALUES (:jpo_fk, :visitor_fk)"
      );
      $stmt->execute([
        ':jpo_fk' => $jpo_id,
        ':visitor_fk' => $visitor_id
      ]);
      return ['id_inscription' => $this->pdo->lastInsertId(), 'message' => 'Inscription successful'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }

  public function delete($jpo_id, $visitor_id)
  {
    session_start();
    if (!isset($_SESSION['visitor'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    if ($_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Cannot unregister for another visitor'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "DELETE FROM inscriptions WHERE jpo_fk = :jpo_fk AND visitor_fk = :visitor_fk"
      );
      $stmt->execute([
        ':jpo_fk' => $jpo_id,
        ':visitor_fk' => $visitor_id
      ]);
      if ($stmt->rowCount() === 0) {
        http_response_code(404);
        return ['error' => 'Inscription not found'];
      }
      return ['message' => 'Unregistration successful'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }

  public function getAll()
  {
    session_start();
    if (!isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    $query = "SELECT i.id_inscription, i.jpo_fk, i.visitor_fk, i.created_at, 
                         j.title AS jpo_title, v.first_name, v.last_name, v.email
                  FROM inscriptions i
                  JOIN jpo j ON i.jpo_fk = j.id_jpo
                  JOIN visitors v ON i.visitor_fk = v.id_visitors";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>