
<?php
require_once __DIR__ . '/../config/Database.php';

class Comment
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  public function create($jpo_id, $visitor_id, $comment)
  {
    session_start();
    if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    // Récupérer l'email de l'utilisateur connecté
    $user_email = isset($_SESSION['visitor']) ? $_SESSION['visitor']['email'] : $_SESSION['admin']['email'];

    // Vérifier que visitor_id correspond à l'email de l'utilisateur connecté
    $stmt = $this->pdo->prepare("SELECT id_visitors FROM visitors WHERE id_visitors = :visitor_id AND email = :email");
    $stmt->execute([':visitor_id' => $visitor_id, ':email' => $user_email]);
    if (!$stmt->fetch()) {
      http_response_code(403);
      return ['error' => 'Cannot comment for another user'];
    }

    if (empty($comment)) {
      http_response_code(400);
      return ['error' => 'Comment is required'];
    }

    $stmt = $this->pdo->prepare("SELECT id_jpo FROM jpo WHERE id_jpo = :jpo_id");
    $stmt->execute([':jpo_id' => $jpo_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid JPO'];
    }

    // Approuver automatiquement si admin
    $is_approved = isset($_SESSION['admin']) ? 1 : 0;

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO comments (jpo_fk, visitor_fk, comment, is_approved) 
                 VALUES (:jpo_fk, :visitor_fk, :comment, :is_approved)"
      );
      $stmt->execute([
        ':jpo_fk' => $jpo_id,
        ':visitor_fk' => $visitor_id,
        ':comment' => $comment,
        ':is_approved' => $is_approved
      ]);
      $message = $is_approved ? 'Comment posted and approved' : 'Comment submitted, awaiting moderation';
      return ['id_comments' => $this->pdo->lastInsertId(), 'message' => $message];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }

  public function getAll($jpo_id)
  {
    $query = "SELECT c.id_comments, c.comment, c.created_at, v.first_name, v.last_name
                  FROM comments c
                  JOIN visitors v ON c.visitor_fk = v.id_visitors
                  WHERE c.jpo_fk = :jpo_id AND c.is_approved = 1";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([':jpo_id' => $jpo_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function moderate($comment_id, $approve)
  {
    session_start();
    if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Directeur') {
      http_response_code(403);
      return ['error' => 'Only Directors can moderate comments'];
    }

    try {
      if ($approve) {
        $stmt = $this->pdo->prepare("UPDATE comments SET is_approved = 1 WHERE id_comments = :id_comments");
      } else {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id_comments = :id_comments");
      }
      $stmt->execute([':id_comments' => $comment_id]);
      if ($stmt->rowCount() === 0) {
        http_response_code(404);
        return ['error' => 'Comment not found'];
      }
      return ['message' => $approve ? 'Comment approved' : 'Comment rejected'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }
}
?>