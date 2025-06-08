
<?php
require_once __DIR__ . '/../config/Database.php';

class Visitor
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  public function login($email, $password = null)
  {
    $stmt = $this->pdo->prepare("SELECT id_visitors, first_name, last_name, email FROM visitors WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$visitor) {
      http_response_code(401);
      return ['error' => 'Invalid email'];
    }

    // Si password est requis, vérifier (sinon, ignorer)
    if ($password !== null) {
      $stmt = $this->pdo->prepare("SELECT password FROM visitors WHERE email = :email");
      $stmt->execute([':email' => $email]);
      $hashed_password = $stmt->fetchColumn();
      if (!password_verify($password, $hashed_password)) {
        http_response_code(401);
        return ['error' => 'Invalid password'];
      }
    }

    session_start();
    $_SESSION['visitor'] = [
      'id' => $visitor['id_visitors'],
      'email' => $visitor['email'],
      'first_name' => $visitor['first_name'],
      'last_name' => $visitor['last_name']
    ];
    return ['id' => $visitor['id_visitors'], 'email' => $visitor['email'], 'message' => 'Login successful'];
  }
}
?>