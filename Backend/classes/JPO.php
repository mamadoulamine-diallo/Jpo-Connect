```php
<?php
require_once __DIR__ . '/../config/Database.php';

class Jpo
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  public function getAll()
  {
    session_start();
    if (!isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    $query = "SELECT j.id_jpo, j.title, j.date_jpo, j.description, j.created_at, 
                         s.city, s.address, s.cp, s.phone, 
                         a.first_name, a.last_name, a.email AS admin_email
                  FROM jpo j 
                  JOIN site s ON j.site_fk = s.id_site 
                  JOIN admin a ON j.created_by = a.id_admin";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create($title, $date_jpo, $site_id, $description)
  {
    session_start();
    if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Directeur') {
      http_response_code(403);
      return ['error' => 'Only Directors can create JPOs'];
    }

    if (empty($title) || empty($date_jpo) || empty($site_id)) {
      http_response_code(400);
      return ['error' => 'Title, date, and site are required'];
    }

    $stmt = $this->pdo->prepare("SELECT id_site FROM site WHERE id_site = :site_id");
    $stmt->execute([':site_id' => $site_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid site'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO jpo (title, date_jpo, site_fk, description, created_by) 
                 VALUES (:title, :date_jpo, :site_id, :description, :created_by)"
      );
      $stmt->execute([
        ':title' => $title,
        ':date_jpo' => $date_jpo,
        ':site_id' => $site_id,
        ':description' => $description ?: null,
        ':created_by' => $_SESSION['admin']['id']
      ]);
      return ['id_jpo' => $this->pdo->lastInsertId(), 'message' => 'JPO created successfully'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }
}
?>