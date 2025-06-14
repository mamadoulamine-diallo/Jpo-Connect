<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Chemin corrigé
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User
{
  private $db;
  private $jwt_secret = 'Kj9mP2nQ7rT4vY6uW3jN1zX8cV0bH2iPqR5sE9tF6gJ3dK1hL8mN0oP3qR4sT7u'; // Ta clé générée

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getConnection();
  }

  // Méthode pour exécuter une requête préparée
  public function prepareAndExecute($query, $params = [])
  {
    try {
      $stmt = $this->db->prepare($query);
      foreach ($params as $key => $value) {
        $stmt->bindParam($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
      }
      $stmt->execute();
      return $stmt;
    } catch (PDOException $e) {
      return false; // Gère l'échec de la préparation/exécution
    }
  }

  // Connexion de l'utilisateur
  public function login($username, $password)
  {
    try {
      $query = "SELECT id, username, password, role FROM users WHERE username = :username";
      $stmt = $this->prepareAndExecute($query, [':username' => $username]);
      if ($stmt === false) {
        return ['success' => false, 'error' => 'Erreur de connexion à la base de données'];
      }

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
        // session_start() est géré dans auth.php, pas ici
        return ['success' => true, 'message' => 'Connexion réussie', 'role' => $user['role']];
      } else {
        return ['success' => false, 'error' => 'Identifiants incorrects'];
      }
    } catch (Exception $e) {
      return ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()];
    }
  }

  // Méthodes JWT (peuvent être laissées pour l'instant, mais non utilisées)
  private function generateJWT($user_id, $role)
  {
    $payload = ['user_id' => $user_id, 'role' => $role, 'exp' => time() + 3600];
    return JWT::encode($payload, $this->jwt_secret, 'HS256');
  }

  public function verifyJWT($token)
  {
    try {
      $decoded = JWT::decode($token, new Key($this->jwt_secret, 'HS256'));
      return (array) $decoded;
    } catch (Exception $e) {
      return false; // Évite var_dump pour la production
    }
  }
}
