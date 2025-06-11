<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/EmailService.php';

class Visitor
{
    private $pdo;
    private $emailService;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
        $this->emailService = new EmailService();
    }

    public function create($nom, $prenom, $email, $jpo_id = null)
    {
        try {
            $this->pdo->beginTransaction();

            // Vérifier si le visiteur existe déjà
            $stmt = $this->pdo->prepare("SELECT id_visitors FROM visitors WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            $visitor_id = null;

            if ($existing) {
                $visitor_id = $existing['id_visitors'];
            } else {
                // Créer un nouveau visiteur
                $stmt = $this->pdo->prepare(
                    "INSERT INTO visitors (first_name, last_name, email) 
                     VALUES (:prenom, :nom, :email)"
                );
                
                $stmt->execute([
                    ':prenom' => $prenom,
                    ':nom' => $nom,
                    ':email' => $email
                ]);

                $visitor_id = $this->pdo->lastInsertId();
            }

            if ($jpo_id) {
                //  détails de la JPO pour l'email
           $stmt = $this->pdo->prepare("
    SELECT j.*, s.address, s.cp, s.city 
    FROM jpo j
    LEFT JOIN site s ON j.site_fk = s.id_site 
    WHERE j.id_jpo = :jpo_id
");

                $stmt->execute([':jpo_id' => $jpo_id]);
                $jpoDetails = $stmt->fetch(PDO::FETCH_ASSOC);

                // Vérifier si l'inscription existe déjà
                $stmt = $this->pdo->prepare(
                    "SELECT id_inscription FROM inscriptions 
                     WHERE visitor_fk = :visitor_id AND jpo_fk = :jpo_id"
                );
                $stmt->execute([
                    ':visitor_id' => $visitor_id,
                    ':jpo_id' => $jpo_id
                ]);

                if (!$stmt->fetch()) {
                    // Créer l'inscription
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO inscriptions (visitor_fk, jpo_fk) 
                         VALUES (:visitor_id, :jpo_id)"
                    );
                    $stmt->execute([
                        ':visitor_id' => $visitor_id,
                        ':jpo_id' => $jpo_id
                    ]);

                    // Envoyer l'email de confirmation
                    $emailSent = $this->emailService->sendJpoConfirmation(
                        $email,
                        $prenom . ' ' . $nom,
                        $jpoDetails
                    );

                    if (!$emailSent) {
                        error_log("Échec de l'envoi du mail de confirmation à: $email");
                    }
                }
            }

            // Créer la session
            if (!isset($_SESSION)) {
                session_start();
            }
            
            $_SESSION['visitor'] = [
                'id' => $visitor_id,
                'email' => $email,
                'first_name' => $prenom,
                'last_name' => $nom
            ];

            $this->pdo->commit();

            return [
                'success' => true,
                'visitor_id' => $visitor_id,
                'message' => 'Visitor created and confirmation email sent'
            ];

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            return ['error' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }

    
  
    public function getInscriptions($visitor_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT j.* 
                 FROM jpo j 
                 INNER JOIN inscriptions i ON j.id_jpo = i.jpo_fk 
                 WHERE i.visitor_fk = :visitor_id"
            );
            $stmt->execute([':visitor_id' => $visitor_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            http_response_code(500);
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

  
}
?>