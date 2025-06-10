<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Inscription
{
  private $pdo;

  public function __construct()
  {
    $db = new Database();
    $this->pdo = $db->getConnection();
  }

  public function create($jpo_id, $visitor_id)
  {
    session_start();
    if (!isset($_SESSION['visitor'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    if ($_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Cannot register for another visitor'];
    }

    $stmt = $this->pdo->prepare("SELECT id_jpo FROM jpo WHERE id_jpo = :jpo_id");
    $stmt->execute([':jpo_id' => $jpo_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid JPO'];
    }

    $stmt = $this->pdo->prepare("SELECT id_visitors FROM visitors WHERE id_visitors = :visitor_id");
    $stmt->execute([':visitor_id' => $visitor_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      return ['error' => 'Invalid visitor'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "INSERT INTO inscriptions (jpo_fk, visitor_fk) 
         VALUES (:jpo_fk, :visitor_fk)"
      );
      $stmt->execute([
        ':jpo_fk' => $jpo_id,
        ':visitor_fk' => $visitor_id
      ]);
      $lastId = $this->pdo->lastInsertId();

 
      $visitorStmt = $this->pdo->prepare("SELECT first_name, last_name, email FROM visitors WHERE id_visitors = :visitor_id");
      $visitorStmt->execute([':visitor_id' => $visitor_id]);
      $visitor = $visitorStmt->fetch(PDO::FETCH_ASSOC);

   
      $jpoStmt = $this->pdo->prepare("SELECT title, date FROM jpo WHERE id_jpo = :jpo_id");
      $jpoStmt->execute([':jpo_id' => $jpo_id]);
      $jpo = $jpoStmt->fetch(PDO::FETCH_ASSOC);

      if ($visitor && $jpo) {
        try {
          $mail = new PHPMailer(true);
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'liveyupengsebastien@gmail.com';
          $mail->Password = 'iohlfqpdxpxmtevf'; // a securiser
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = 587;

          $mail->setFrom('liveyupengsebastien@gmail.com', 'JPO Connect');
          $mail->addAddress($visitor['email'], $visitor['first_name'] . ' ' . $visitor['last_name']);

          $mail->isHTML(true);
          $mail->Subject = 'Confirmation d\'inscription - ' . $jpo['title'];
          $mail->Body = '
            <div style="font-family: Arial, sans-serif;">
              <h2 style="color: #2563eb;">🎓 Confirmation d\'inscription à la JPO</h2>
              <p>Bonjour ' . htmlspecialchars($visitor['first_name']) . ',</p>
              <p>Merci pour votre inscription à la Journée Portes Ouvertes :</p>
              <ul>
                <li><strong>Événement :</strong> ' . htmlspecialchars($jpo['title']) . '</li>
                <li><strong>Date :</strong> ' . htmlspecialchars($jpo['date']) . '</li>
              </ul>
              <p>Nous avons bien enregistré votre participation. À très bientôt !</p>
              <br>
              <p style="font-size: 14px; color: #6b7280;"><em>JPO Connect - Plateforme officielle des Journées Portes Ouvertes</em></p>
            </div>
          ';
          $mail->send();
        } catch (Exception $e) {
          error_log("Erreur envoi mail PHPMailer : " . $mail->ErrorInfo);
        }
      }

      return ['id_inscription' => $lastId, 'message' => 'Inscription réussie. Un email de confirmation a été envoyé.'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }

  public function delete($jpo_id, $visitor_id)
  {
    session_start();
    if (!isset($_SESSION['visitor'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    if ($_SESSION['visitor']['id'] !== $visitor_id) {
      http_response_code(403);
      return ['error' => 'Cannot unregister for another visitor'];
    }

    try {
      $stmt = $this->pdo->prepare(
        "DELETE FROM inscriptions WHERE jpo_fk = :jpo_fk AND visitor_fk = :visitor_fk"
      );
      $stmt->execute([
        ':jpo_fk' => $jpo_id,
        ':visitor_fk' => $visitor_id
      ]);
      if ($stmt->rowCount() === 0) {
        http_response_code(404);
        return ['error' => 'Inscription not found'];
      }
      return ['message' => 'Désinscription réussie.'];
    } catch (\Exception $e) {
      http_response_code(500);
      return ['error' => 'Server error: ' . $e->getMessage()];
    }
  }

  public function getAll()
  {
    session_start();
    if (!isset($_SESSION['admin'])) {
      http_response_code(401);
      return ['error' => 'Unauthorized'];
    }

    $query = "SELECT i.id_inscription, i.jpo_fk, i.visitor_fk, i.created_at, 
                     j.title AS jpo_title, v.first_name, v.last_name, v.email
              FROM inscriptions i
              JOIN jpo j ON i.jpo_fk = j.id_jpo
              JOIN visitors v ON i.visitor_fk = v.id_visitors";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
