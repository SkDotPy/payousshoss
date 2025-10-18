<?php
$dsn = "mysql:host=localhost;dbname=esgi_site;charset=utf8";
$username = "esgiadmin";
$password = "esgiadmin";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2 style='color:green'>✅ Connexion réussie à la base de données !</h2>";
} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ Erreur de connexion : " . $e->getMessage() . "</h2>";
}
?>
