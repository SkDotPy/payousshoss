<?php
/**
 * Fonction d'envoi d'email via SMTP
 * Utilise les constantes définies dans config.php
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer (si vous utilisez Composer)
// require_once __DIR__ . '/../../vendor/autoload.php';

// OU inclure manuellement PHPMailer si pas de Composer
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';

/**
 * Envoie un email via SMTP
 * 
 * @param string $to Email du destinataire
 * @param string $subject Sujet de l'email
 * @param string $body Corps de l'email (HTML)
 * @param array $attachments Tableau de chemins de fichiers à joindre (optionnel)
 * @return bool True si envoyé, False sinon
 */
function sendEmail($to, $subject, $body, $attachments = []) {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Expéditeur
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        
        // Destinataire
        $mail->addAddress($to);
        
        // Répondre à
        $mail->addReplyTo(SMTP_FROM, SMTP_FROM_NAME);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        // Pièces jointes
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                if (file_exists($file)) {
                    $mail->addAttachment($file);
                }
            }
        }

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Erreur envoi email: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Envoie un email simple en texte brut
 */
function sendSimpleEmail($to, $subject, $message) {
    $headers = [
        'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM . '>',
        'Reply-To: ' . SMTP_FROM,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}
