<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
       
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; 
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'liveyupengsebastien@gmail.com';
        $this->mail->Password = 'iohlfqpdxpxmtevf'; 
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->CharSet = 'UTF-8';
    }

    public function sendJpoConfirmation($visitorEmail, $visitorName, $jpoDetails) {
        try {
            $this->mail->setFrom('liveyupengsebastien@gmail.com', 'LaPlateforme_');
            $this->mail->addAddress($visitorEmail, $visitorName);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Confirmation de votre inscription à la JPO';
            
            // corps de mail
            $this->mail->Body = $this->getEmailTemplate($visitorName, $jpoDetails);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur d'envoi d'email: " . $e->getMessage());
            return false;
        }
    }

private function getEmailTemplate($visitorName, $jpoDetails) {
    $date = new DateTime($jpoDetails['date_jpo']);
    return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;'>
            <div style='max-width: 600px; margin: 20px auto; background-color: white; padding: 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <!-- Header -->
               

                <!-- Content -->
                <div style='padding: 0 15px;'>
                    <h1 style='color: #0161FF; margin-bottom: 25px; font-size: 24px; text-align: center;'>
                        <i class='fas fa-check-circle' style='color: #4CAF50; margin-right: 10px;'></i>
                        Confirmation d'inscription
                    </h1>
                    
                    <p style='color: #333; font-size: 16px; margin-bottom: 25px;'>
                        Bonjour <strong>{$visitorName}</strong>,
                    </p>
                    
                    <p style='color: #555; line-height: 1.6;'>
                        Nous vous confirmons votre inscription à notre prochaine Journée Portes Ouvertes.
                    </p>

                    <!-- Event Details Box -->
                    <div style='background-color: #f8f9fa; 
                              border-left: 4px solid #0161FF; 
                              padding: 20px; 
                              margin: 25px 0; 
                              border-radius: 8px;'>
                        <h3 style='color: #0161FF; margin-top: 0; font-size: 18px;'>
                            <i class='fas fa-info-circle' style='margin-right: 10px;'></i>
                            Détails de l'événement
                        </h3>
                        <div style='margin-left: 25px;'>
                            <p style='margin: 15px 0;'>
                                <i class='far fa-calendar-alt' style='color: #0161FF; margin-right: 10px;'></i>
                                <strong>Date :</strong> {$date->format('d/m/Y')}
                            </p>
                            <p style='margin: 15px 0;'>
                                <i class='fas fa-map-marker-alt' style='color: #0161FF; margin-right: 10px;'></i>
                                <strong>Lieu :</strong> {$jpoDetails['address']}, {$jpoDetails['cp']} {$jpoDetails['city']}
                            </p>
                        </div>
                    </div>

                    <!-- Reminder Notice -->
                    <div style='background-color: #e3f2fd; 
                             padding: 15px; 
                             border-radius: 8px; 
                             margin: 25px 0;
                             text-align: center;'>
                        <i class='fas fa-bell' style='color: #0161FF; margin-right: 10px;'></i>
                        Un rappel vous sera envoyé la veille de l'événement
                    </div>

                    <!-- Footer -->
                    <div style='margin-top: 40px; 
                              padding-top: 20px; 
                              border-top: 1px solid #eee; 
                              text-align: center; 
                              color: #666;'>
                        <div style='margin: 15px 0;'>
                            <a href='https://www.facebook.com/LaPlateformeIO' style='color: #0161FF; margin: 0 10px; text-decoration: none;'>
                                <i class='fab fa-facebook-square fa-2x'></i>
                            </a>
                            <a href='https://www.linkedin.com/school/laplateformeio/' style='color: #0161FF; margin: 0 10px; text-decoration: none;'>
                                <i class='fab fa-linkedin fa-2x'></i>
                            </a>
                            <a href='https://twitter.com/LaPlateformeIO' style='color: #0161FF; margin: 0 10px; text-decoration: none;'>
                                <i class='fab fa-twitter-square fa-2x'></i>
                            </a>
                        </div>
                        <p style='margin: 5px 0; font-size: 12px;'>
                            <i class='fas fa-school' style='margin-right: 5px;'></i>
                            LaPlateforme_ - L'école du numérique
                        </p>
                        <p style='margin: 5px 0; font-size: 12px;'>
                            <i class='fas fa-map-pin' style='margin-right: 5px;'></i>
                            8 rue d'hozier, 13002 Marseille
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>
    ";
}
}