```php
<?php
require_once __DIR__ . '/../config/Database.php';

class Comment
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = (new Database())->getConnection();
  }

  public function create($visitor_id, $jpo_id, $content)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['visitor']) || $_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO comments (visitor_fk, jpo_fk, content) VALUES (:visitor_id, :jpo_id, :content)"
      );
      $stmt->execute([
        ':visitor_id' => $visitor_id,
        ':jpo_id' => $jpo_id,
        ':content' => $content
      ]);
      return ['message' => 'Commentaire ajouté avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function moderate($comment_id, $is_approved)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['comment_moderate'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "UPDATE comments SET is_approved = :is_approved WHERE id_comment = :comment_id"
      );
      $stmt->execute([
        ':comment_id' => $comment_id,
        ':is_approved' => $is_approved
      ]);
      return ['message' => 'Commentaire modéré avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function reply($comment_id, $content)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['comment_reply'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO comment_replies (comment_fk, admin_fk, content) 
                 VALUES (:comment_id, :admin_id, :content)"
      );
      $stmt->execute([
        ':comment_id' => $comment_id,
        ':admin_id' => $_SESSION['admin']['id'],
        ':content' => $content
      ]);
      return ['message' => 'Réponse ajoutée avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function getPending()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['comment_moderate'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "SELECT c.id_comment, c.content, j.title AS jpo_title
                 FROM comments c
                 JOIN jpo j ON c.jpo_fk = j.id_jpo
                 WHERE c.is_approved = 0"
      );
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }
}
?>