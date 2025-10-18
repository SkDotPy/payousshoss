
<?php
/** @var string $prenom */
/** @var string $animal_nom */
/** @var string $animal_type */
/** @var string $date_adoption */
/** @var string $dossier_num */
?>
<!doctype html>
<html lang="fr"><meta charset="utf-8">
<body style="font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;background:#f6f7fb;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
    <tr><td align="center">
      <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden">
        <tr>
          <td style="background:#059669;color:#fff;padding:24px 28px;font-size:20px;font-weight:700">
            Félicitations pour l’adoption de <?= htmlspecialchars($animal_nom) ?> 🎉❤️
          </td>
        </tr>
        <tr>
          <td style="padding:28px;color:#111">
            <p style="margin:0 0 12px 0;font-size:16px;">Bonjour <?= htmlspecialchars($prenom) ?>,</p>
            <p style="margin:0 0 16px 0;font-size:16px;">C’est officiel : <?= htmlspecialchars($animal_nom) ?> a trouvé sa famille !</p>

            <p style="margin:0 8px 8px 0;font-weight:600;">Détails de votre adoption :</p>
            <ul style="margin:0 0 16px 20px;padding:0;font-size:15px;line-height:1.6">
              <li>Animal adopté : <?= htmlspecialchars($animal_nom) ?></li>
              <li>Espèce/Race : <?= htmlspecialchars($animal_type) ?></li>
              <li>Date d’adoption : <?= htmlspecialchars($date_adoption) ?></li>
              <li>Numéro de dossier : <?= htmlspecialchars($dossier_num) ?></li>
            </ul>

            <p style="margin:0 0 8px 0;font-weight:600;">Prochaines étapes :</p>
            <ul style="margin:0 0 16px 20px;padding:0;font-size:15px;line-height:1.6">
              <li>Réception d’un email avec le rendez-vous de remise</li>
              <li>Liste des documents à apporter</li>
              <li>Carnet de santé & infos médicales</li>
              <li>Conseils d’adaptation à la maison</li>
            </ul>

            <p style="margin:0 0 16px 0;">Nous restons disponibles pour vous accompagner. Écrivez-nous à <a href="mailto:contact@paw-connect.org">contact@paw-connect.org</a>.</p>
            <p style="margin:0 0 16px 0;">Partagez votre bonheur avec <strong>#PawConnect</strong> !</p>

            <p style="margin:0;">Avec toute notre affection,<br>L’équipe PawConnect 🐾</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
