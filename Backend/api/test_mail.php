<?php
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "🚀 Test PHPMailer JPO Connect\n";
echo str_repeat("=", 40) . "\n";

$mail = new PHPMailer(true);

try {
    // Configuration SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'liveyupengsebastien@gmail.com';
    $mail->Password = 'iohlfqpdxpxmtevf'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinataires
    $mail->setFrom('liveyupengsebastien@gmail.com', 'JPO Connect');
    $mail->addAddress('liveyupengsebastien@gmail.com', 'Test JPO'); 

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Test JPO Connect - ' . date('H:i:s');
    $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #2563eb;">🎓 Test JPO Connect</h2>
            <p>Bonjour,</p>
            <p>je test jus PhpMailer hu.</p>
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h3>✅  :</h3>
                <ul>
                  
                    <li>Heure du test : ' . date('Y-m-d H:i:s') . '</li>
                </ul>
            </div>
            <p>Jsuis un goat si ca marche !</p>
            <hr style="margin: 30px 0;">
            <p style="color: #64748b; font-size: 14px;">
                <em>JPO Connect - Système de gestion des Journées Portes Ouvertes</em>
            </p>
        </div>
    ';

    $mail->send();
    
    echo "✅ SUCCESS: Email envoyé avec succès !\n";
    echo "📧 Vérifiez votre boîte de réception\n";
    echo "⏰ Heure d'envoi : " . date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo "❌ ERREUR lors de l'envoi\n";
    echo "💬 Message d'erreur : " . $e->getMessage() . "\n";
    echo "🔧 Détails PHPMailer : " . $mail->ErrorInfo . "\n";
    
    // Suggestions de dépannage
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "🛠️  DÉPANNAGE :\n";
    echo "1. Vérifiez que le mot de passe d'app est correct (16 caractères)\n";
    echo "2. Assurez-vous que l'authentification 2FA est active\n";
    echo "3. Vérifiez votre connexion internet\n";
    echo "4. Essayez de vous connecter à Gmail depuis un navigateur\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "Test terminé.\n";
?>