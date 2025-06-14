<?php
require_once __DIR__ . '/../config/database.php';

class Content
{
  private $db;

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getConnection();
  }

  // Obtenir un contenu par clé
  public function get($key)
  {
    if ($key === null) {
      return null; // Retourne null si la clé est invalide
    }

    $query = "SELECT value FROM contents WHERE `key` = :key LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':key', $key, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : '';
  }

  // Mettre à jour un contenu
  public function update($key, $value, $user_role)
  {
    if ($user_role !== 'director' && $user_role !== 'manager') {
      return ['error' => 'Permission refusée'];
    }

    if ($key === null || $value === null) {
      return ['error' => 'Clé ou valeur manquante'];
    }

    $query = "INSERT INTO contents (`key`, value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE value = :value";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':key', $key, PDO::PARAM_STR);
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return ['success' => true, 'message' => 'Contenu mis à jour'];
    } else {
      return ['error' => 'Erreur lors de la mise à jour'];
    }
  }
}
