<?php /** @var string $prenom */ ?>
<!doctype html>
<html lang="fr"><meta charset="utf-8">
<body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f6f7fb;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
    <tr><td align="center">
      <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden">
        <tr>
          <td style="background:#1E3A8A;color:#fff;padding:24px 28px;font-size:20px;font-weight:700">
            Bienvenue sur PawConnect 🐾
          </td>
        </tr>
        <tr>
          <td style="padding:28px;color:#111">
            <p style="margin:0 0 12px 0;font-size:16px;">Bonjour <?= htmlspecialchars($prenom) ?>,</p>
            <p style="margin:0 0 16px 0;font-size:16px;">Nous sommes ravis de vous accueillir parmi nous ! En créant votre compte sur PawConnect, vous faites le premier pas vers une belle aventure : offrir une seconde chance à un animal qui attend son foyer pour la vie.</p>

            <p style="margin:0 0 8px 0;font-weight:600;">Ce que vous pouvez faire dès maintenant :</p>
            <ul style="margin:0 0 16px 20px;padding:0;font-size:15px;line-height:1.6">
              <li>Parcourir les profils de nos adorables pensionnaires</li>
              <li>Créer des alertes personnalisées selon vos critères</li>
              <li>Sauvegarder vos coups de cœur</li>
              <li>Contacter nos équipes pour toute question</li>
            </ul>

            <p style="margin:0 0 16px 0;font-size:16px;">Chaque année, des milliers d'animaux attendent une famille aimante. Grâce à des personnes comme vous, nous pouvons leur offrir l'espoir d'un avenir meilleur.</p>
            <p style="margin:0 0 16px 0;font-size:16px;">Besoin d’aide ? Consultez notre FAQ ou écrivez-nous à <a href="mailto:contact@paw-connect.org">contact@paw-connect.org</a>.</p>

            <p style="margin:0 0 16px 0;">Avec toute notre gratitude,<br>L’équipe PawConnect</p>
            <p style="margin:0 0 0 0;font-size:14px;color:#555;">P.S. : Suivez-nous sur les réseaux pour les belles histoires d’adoption !</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
