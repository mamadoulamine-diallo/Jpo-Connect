
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

  public function getAll($city = null, $date = null, $start_date = null, $end_date = null, $keyword = null)
  {
    session_start();
    $isAdmin = isset($_SESSION['admin']);

    $query = "SELECT j.id_jpo, j.title, j.date_jpo, j.description, j.created_at, 
                         s.city, s.address, s.cp, s.phone";
    if ($isAdmin) {
      $query .= ", a.first_name, a.last_name, a.email AS admin_email";
    }
    $query .= " FROM jpo j 
                    JOIN site s ON j.site_fk = s.id_site";
    if ($isAdmin) {
      $query .= " JOIN admin a ON j.created_by = a.id_admin";
    }

    $params = [];
    $conditions = [];
    if ($city) {
      $conditions[] = "s.city = :city";
      $params[':city'] = $city;
    }
    if ($date) {
      $conditions[] = "j.date_jpo = :date";
      $params[':date'] = $date;
    }
    if ($start_date && $end_date) {
      $conditions[] = "j.date_jpo BETWEEN :start_date AND :end_date";
      $params[':start_date'] = $start_date;
      $params[':end_date'] = $end_date;
    }
    if ($keyword) {
      $conditions[] = "(j.title LIKE :keyword OR j.description LIKE :keyword)";
      $params[':keyword'] = '%' . $keyword . '%';
    }
    if (!empty($conditions)) {
      $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
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