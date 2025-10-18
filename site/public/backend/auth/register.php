<?php
session_start();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../lib/mailer.php';

header('Content-Type: text/html; charset=utf-8');

$nom       = trim($_POST['nom'] ?? '');
$age       = trim($_POST['age'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = trim($_POST['password'] ?? '');
$confirm   = trim($_POST['password_confirm'] ?? '');
$captcha   = trim($_POST['captcha'] ?? '');
$is_refuge = !empty($_POST['is_refuge']) ? 1 : 0;

// Validation basique
if (empty($nom) || empty($age) || empty($email) || empty($password) || empty($confirm) || empty($captcha)) {
    die('Tous les champs sont obligatoires.');
}
if ($password !== $confirm) {
    die('Les mots de passe ne correspondent pas.');
}
if (empty($_SESSION['captcha_answer']) || strcasecmp($captcha, $_SESSION['captcha_answer']) !== 0) {
    die('Captcha incorrect.');
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die('Cet email est déjà utilisé.');
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = $is_refuge ? 'refuge' : 'user';
    $status = $is_refuge ? 'pending' : 'active';

    // Insertion utilisateur
    $stmt = $pdo->prepare('INSERT INTO users (nom, age, email, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$nom, $age, $email, $password_hash, $role, $status]);

    // Inscription auto à la newsletter
    $pdo->prepare("INSERT INTO newsletter_subscribers (email,name,status) VALUES (?,?, 'active')
                   ON DUPLICATE KEY UPDATE name=VALUES(name), status='active'")
        ->execute([$email, $nom]);

    // Envoi du mail de bienvenue
    $subject = 'Bienvenue sur PawConnect !';
    $text = "Bonjour $nom,\n\nBienvenue sur PawConnect ! Votre compte a bien été créé.\n\nÀ bientôt !";
    $html = "<p>Bonjour <strong>" . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . "</strong>,</p>
             <p>Bienvenue sur <strong>PawConnect</strong> ! Votre compte a bien été créé.</p>
             <p>À bientôt 🐾</p>";

    $res = send_mail($email, $subject, $html, $text);

    // Log du mail de bienvenue
    $pdo->prepare("INSERT INTO newsletter_logs (email,subject,body,type,status,error) VALUES (?,?,?,?,?,?)")
        ->execute([$email, $subject, $text, 'welcome', $res['ok'] ? 'sent' : 'failed', $res['ok'] ? null : ($res['error'] ?? 'mail')]);

    // Si refuge → notifier les admins
    if ($is_refuge) {
        $m = new PHPMailer\PHPMailer\PHPMailer(true);
        $m->isSMTP();
        $m->Host = SMTP_HOST;
        $m->SMTPAuth = true;
        $m->Username = SMTP_USER;
        $m->Password = SMTP_PASS;
        $m->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $m->Port = SMTP_PORT;
        $m->CharSet = 'UTF-8';
        $m->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $m->addAddress(SMTP_FROM, 'Admin PawConnect');
        $m->isHTML(true);
        $m->Subject = 'Validation requise: nouveau refuge';
        $m->Body = '<p>Nouveau refuge à valider:</p><ul><li>Nom: ' . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . '</li><li>Email: ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</li></ul>';
        $m->AltBody = "Nouveau refuge à valider\nNom: $nom\nEmail: $email";
        try { $m->send(); } catch (Exception $e) { error_log('Mail admin error: '.$e->getMessage()); }
    }

    unset($_SESSION['captcha_answer']);

    echo '<h2>Inscription réussie !</h2>
          <p>Un email de confirmation vous a été envoyé à ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '.</p>';

} catch (Throwable $e) {
    error_log('REGISTER ERROR: '.$e->getMessage());
    http_response_code(500);
    die('Erreur serveur.');
}
