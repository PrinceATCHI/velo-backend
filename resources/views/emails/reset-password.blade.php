<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réinitialisation mot de passe</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
    .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: #1a6b6b; padding: 32px 40px; text-align: center; }
    .header h1 { color: #fff; font-size: 22px; margin: 0; }
    .header p { color: rgba(255,255,255,.75); font-size: 13px; margin: 6px 0 0; }
    .body { padding: 36px 40px; }
    .body h2 { font-size: 20px; color: #1a2332; margin: 0 0 12px; }
    .body p { font-size: 14px; color: #5a6a7e; line-height: 1.7; margin: 0 0 20px; }
    .btn { display: block; width: fit-content; margin: 0 auto 24px; background: #1a6b6b; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 9px; font-size: 15px; font-weight: 700; }
    .warning { background: #fef2f2; border-left: 4px solid #e53935; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #c62828; margin-bottom: 16px; }
    .note { font-size: 12px; color: #9aa5b4; text-align: center; }
    .footer { background: #f4f7f9; padding: 18px 40px; text-align: center; font-size: 12px; color: #9aa5b4; border-top: 1px solid #dde3ea; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🚲 FahrradHauskauf</h1>
      <p>Réinitialisation de mot de passe</p>
    </div>
    <div class="body">
      <h2>Bonjour {{ $user->name }},</h2>
      <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.</p>
      <a href="{{ $resetUrl }}" class="btn">🔑 Réinitialiser mon mot de passe</a>
      <div class="warning">
        ⚠️ Ce lien expire dans <strong>60 minutes</strong>. Après ce délai, vous devrez faire une nouvelle demande.
      </div>
      <p class="note">Si vous n'avez pas demandé cette réinitialisation, ignorez cet email. Votre mot de passe reste inchangé.</p>
    </div>
    <div class="footer">
      © {{ date('Y') }} FahrradHauskauf — Tous droits réservés
    </div>
  </div>
</body>
</html>