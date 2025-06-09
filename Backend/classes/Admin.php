<?php
require_once __DIR__ . '/../config/Database.php';


// gère les admins 
class Admin
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  // Connexion : Vérifie l’e-mail et le mot de passe
  public function login($email, $password)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM admin WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
      // On stocke l’admin dans une session
      session_start();
      $_SESSION['admin'] = [
        'id' => $admin['id_admin'],
        'email' => $admin['email'],
        'role' => $admin['role']
      ];
      return [
        'id' => $admin['id_admin'],
        'email' => $admin['email'],
        'role' => $admin['role']
      ];
    }
    return false;
  }
}
