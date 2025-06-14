<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
  private $mailer;

  public function __construct()
  {
    $this->mailer = new PHPMailer(true);
    $this->mailer->isSMTP();
    $this->mailer->Host = 'smtp.example.com'; // Remplacer par ton serveur SMTP
    $this->mailer->SMTPAuth = true;
    $this->mailer->Username = 'your_email@example.com'; // Ton email
    $this->mailer->Password = 'your_password'; // Ton mot de passe
    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $this->mailer->Port = 587;
    $this->mailer->setFrom('your_email@example.com', 'La Plateforme');
  }

  // Envoyer un rappel pour une JPO
  public function sendReminder($jpo_id, $user_email, $jpo_title, $jpo_date, $jpo_location)
  {
    try {
      $this->mailer->addAddress($user_email);
      $this->mailer->isHTML(true);
      $this->mailer->Subject = 'Rappel : JPO La Plateforme';
      $this->mailer->Body = "
                <h1>Rappel de votre inscription</h1>
                <p>Vous êtes inscrit à la Journée Portes Ouvertes :</p>
                <p><strong>Titre :</strong> $jpo_title</p>
                <p><strong>Date :</strong> $jpo_date</p>
                <p><strong>Lieu :</strong> $jpo_location</p>
                <p>Nous avons hâte de vous accueillir !</p>
            ";
      $this->mailer->send();
      return ['success' => 'Email envoyé'];
    } catch (Exception $e) {
      return ['error' => 'Erreur lors de l\'envoi : ' . $e->getMessage()];
    }
  }
}
