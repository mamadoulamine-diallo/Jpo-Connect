
<?php
require_once __DIR__ . '/../config/Database.php';

class Admin
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = (new Database())->getConnection();
  }

  public function login($email, $password)
  {
    $stmt = $this->pdo->prepare("SELECT id_admin, email, role, permissions FROM admin WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin && password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = [
        'id' => $admin['id_admin'],
        'email' => $admin['email'],
        'role' => $admin['role'],
        'permissions' => json_decode($admin['permissions'], true)
      ];
      return ['id' => $admin['id_admin'], 'email' => $admin['email'], 'role' => $admin['role']];
    }
    return false;
  }

  public function updateRole($admin_id, $role, $permissions)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !$_SESSION['admin']['permissions']['role_manage']) {
      http_response_code(403);
      return ['error' => 'Accès réservé aux Directeurs'];
    }

    if (!in_array($role, ['Directeur', 'Responsable', 'Salarié'])) {
      http_response_code(400);
      return ['error' => 'Rôle invalide'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "UPDATE admin SET role = :role, permissions = :permissions WHERE id_admin = :admin_id"
      );
      $stmt->execute([
        ':admin_id' => $admin_id,
        ':role' => $role,
        ':permissions' => json_encode($permissions)
      ]);
      return ['message' => 'Rôle mis à jour avec succès'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }

  public function getAll()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if (!isset($_SESSION['admin']) || !$_SESSION['admin']['permissions']['role_manage']) {
      http_response_code(403);
      return ['error' => 'Accès réservé aux Directeurs'];
    }

    try {
      $stmt = $this->pdo->prepare("SELECT id_admin, email, role, permissions FROM admin");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Erreur serveur : ' . $e->getMessage()];
    }
  }
}
?>