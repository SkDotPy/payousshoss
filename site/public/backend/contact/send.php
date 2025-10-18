<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../utils/mailer.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier le captcha
$captchaInput = $_POST['captcha'] ?? '';
$captchaAnswer = $_SESSION['captcha_answer'] ?? '';

if (empty($captchaInput) || $captchaInput != $captchaAnswer) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Captcha invalide']);
    exit;
}

// Nettoyer le captcha de la session
unset($_SESSION['captcha_answer']);

// Récupérer les données du formulaire
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$sujet = $_POST['sujet'] ?? '';
$message = trim($_POST['message'] ?? '');

// Validation des champs obligatoires
if (empty($nom) || empty($prenom) || empty($email) || empty($sujet) || empty($message)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Tous les champs obligatoires doivent être remplis']);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email invalide']);
    exit;
}

// Sujets disponibles
$sujets = [
    'adoption' => 'Question sur l\'adoption',
    'signalement' => 'Signalement d\'un animal',
    'partenariat' => 'Partenariat',
    'benevole' => 'Devenir bénévole',
    'don' => 'Question sur les dons',
    'autre' => 'Autre'
];

$sujetLabel = $sujets[$sujet] ?? 'Autre';

try {
    // Préparer l'email
    $emailSubject = "[Paw Connect] Message de contact : $sujetLabel";
    
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
            .info-block { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #667eea; }
            .info-label { font-weight: bold; color: #667eea; }
            .message-box { background: #fff; padding: 20px; border-radius: 5px; border: 1px solid #ddd; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>✉️ Nouveau message de contact</h2>
            </div>
            <div class='content'>
                <div class='info-block'>
                    <p><span class='info-label'>Sujet :</span> $sujetLabel</p>
                </div>
                
                <h3>👤 Informations de contact</h3>
                <div class='info-block'>
                    <p><span class='info-label'>Nom :</span> " . htmlspecialchars($nom) . "</p>
                    <p><span class='info-label'>Prénom :</span> " . htmlspecialchars($prenom) . "</p>
                    <p><span class='info-label'>Email :</span> <a href='mailto:$email'>$email</a></p>
                    " . ($telephone ? "<p><span class='info-label'>Téléphone :</span> " . htmlspecialchars($telephone) . "</p>" : "") . "
                </div>
                
                <h3>💬 Message</h3>
                <div class='message-box'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                
                <div class='footer'>
                    <p>Message reçu le " . date('d/m/Y à H:i') . "</p>
                    <p><a href='" . APP_URL . "'>Paw Connect</a> - Agir ensemble, les protéger</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    // Envoyer l'email
    $emailSent = sendEmail('contact@paw-connect.org', $emailSubject, $emailBody);

    if (!$emailSent) {
        throw new Exception('Erreur lors de l\'envoi de l\'email');
    }

    // Envoyer un email de confirmation à l'utilisateur
    $confirmSubject = "Votre message a bien été reçu - Paw Connect";
    $confirmBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🐾 Merci de nous avoir contactés !</h2>
            </div>
            <div class='content'>
                <p>Bonjour " . htmlspecialchars($prenom) . ",</p>
                <p>Nous avons bien reçu votre message concernant : <strong>$sujetLabel</strong></p>
                <p>Notre équipe vous répondra dans les plus brefs délais.</p>
                <p>Cordialement,<br>L'équipe Paw Connect</p>
                <div class='footer'>
                    <p><a href='" . APP_URL . "'>Paw Connect</a> - Agir ensemble, les protéger</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    sendEmail($email, $confirmSubject, $confirmBody);

    // Réponse succès
    echo json_encode([
        'ok' => true,
        'success' => true,
        'message' => 'Votre message a été envoyé avec succès'
    ]);

} catch (Throwable $e) {
    error_log('CONTACT ERROR: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur lors de l\'envoi du message']);
}
