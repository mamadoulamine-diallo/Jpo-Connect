<?php
require_once __DIR__ . '/../config/Database.php';

// Cette classe gère les JPOs (les événements)
class JPO
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  // Lister les JPOs, avec filtres par ville ou date
  public function getAll($city = '', $date = '')
  {
    $query = "SELECT j.id_jpo, j.date_jpo, s.city, s.address, s.cp, s.phone 
                  FROM jpo j 
                  JOIN site s ON j.site_fk = s.id_site 
                  WHERE 1=1";
    $params = [];
    if ($city) {
      $query .= " AND s.city LIKE :city";
      $params[':city'] = "%$city%";
    }
    if ($date) {
      $query .= " AND j.date_jpo = :date";
      $params[':date'] = $date;
    }
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Créer une JPO (Directeur seulement)
  public function create($site_id, $admin_id, $date_jpo)
  {
    session_start();
    if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Directeur') {
      return ['error' => 'Seul un Directeur peut créer une JPO'];
    }
    // Vérifier si le site existe
    $stmt = $this->pdo->prepare("SELECT id_site FROM site WHERE id_site = :site_id");
    $stmt->execute([':site_id' => $site_id]);
    if (!$stmt->fetch()) {
      return ['error' => 'Site invalide'];
    }
    // Vérifier si l’admin existe
    $stmt = $this->pdo->prepare("SELECT id_admin FROM admin WHERE id_admin = :admin_id");
    $stmt->execute([':admin_id' => $admin_id]);
    if (!$stmt->fetch()) {
      return ['error' => 'Admin invalide'];
    }
    // Créer la JPO
    $stmt = $this->pdo->prepare(
      "INSERT INTO jpo (site_fk, admin_fk, date_jpo) 
             VALUES (:site_id, :admin_id, :date_jpo)"
    );
    $stmt->execute([
      ':site_id' => $site_id,
      ':admin_id' => $admin_id,
      ':date_jpo' => $date_jpo
    ]);
    return ['id' => $this->pdo->lastInsertId()];
  }
}
