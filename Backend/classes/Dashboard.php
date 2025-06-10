
<?php
require_once __DIR__ . '/../config/Database.php';

class Dashboard
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = (new Database())->getConnection();
  }

  public function getData()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['stats_view'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    $admin_id = $_SESSION['admin']['id'];

    $stmt = $this->pdo->prepare(
      "SELECT j.id_jpo, j.title, j.date_jpo, j.description, s.city, j.capacity,
                    COUNT(i.id_inscription) AS inscrits
             FROM jpo j
             JOIN site s ON j.site_fk = s.id_site
             LEFT JOIN inscriptions i ON j.id_jpo = i.jpo_fk
             WHERE j.created_by = :admin_id
             GROUP BY j.id_jpo"
    );
    $stmt->execute([':admin_id' => $admin_id]);
    $jpos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $this->pdo->prepare(
      "SELECT COUNT(DISTINCT j.id_jpo) AS total_jpos,
                    COUNT(i.id_inscription) AS total_inscrits
             FROM jpo j
             LEFT JOIN inscriptions i ON j.id_jpo = i.jpo_fk
             WHERE j.created_by = :admin_id"
    );
    $stmt->execute([':admin_id' => $admin_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
      'jpos' => $jpos,
      'stats' => $stats
    ];
  }

  public function deleteJpo($jpo_id)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['jpo_delete'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    $admin_id = $_SESSION['admin']['id'];

    $stmt = $this->pdo->prepare(
      "SELECT id_jpo FROM jpo WHERE id_jpo = :jpo_id AND created_by = :admin_id"
    );
    $stmt->execute([':jpo_id' => $jpo_id, ':admin_id' => $admin_id]);
    if (!$stmt->fetch()) {
      http_response_code(404);
      return ['error' => 'JPO introuvable ou non autorisée'];
    }

    try {
      $stmt = $this->pdo->prepare("DELETE FROM jpo WHERE id_jpo = :jpo_id");
      $stmt->execute([':jpo_id' => $jpo_id]);
      return ['message' => 'JPO supprimée avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function updateJpo($jpo_id, $title, $date_jpo, $site_id, $description, $capacity)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !($_SESSION['admin']['permissions']['jpo_edit'] ?? false)) {
      http_response_code(403);
      return ['error' => 'Accès non autorisé'];
    }

    $admin_id = $_SESSION['admin']['id'];

    $stmt = $this->pdo->prepare(
      "SELECT id_jpo FROM jpo WHERE id_jpo = :jpo_id AND created_by = :admin_id"
    );
    $stmt->execute([':jpo_id' => $jpo_id, ':admin_id' => $admin_id]);
    if (!$stmt->fetch()) {
      http_response_code(404);
      return ['error' => 'JPO introuvable ou non autorisée'];
    }

    $stmt = $this->pdo->prepare("SELECT id_site FROM site WHERE id_site = :site_id");
    $stmt->execute([':site_id' => $site_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Site invalide'];
    }

    if (empty($title) || empty($date_jpo)) {
      http_response_code(400);
      return ['error' => 'Titre et date sont requis'];
    }
    if (!is_null($capacity) && (!is_numeric($capacity) || $capacity < 0)) {
      http_response_code(400);
      return ['error' => 'Capacité invalide'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "UPDATE jpo 
                 SET title = :title, date_jpo = :date_jpo, site_fk = :site_id, 
                     description = :description, capacity = :capacity
                 WHERE id_jpo = :jpo_id"
      );
      $stmt->execute([
        ':jpo_id' => $jpo_id,
        ':title' => $title,
        ':date_jpo' => $date_jpo,
        ':site_id' => $site_id,
        ':description' => $description ?: null,
        ':capacity' => $capacity ?: null
      ]);
      return ['message' => 'JPO mise à jour avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }
}
?>