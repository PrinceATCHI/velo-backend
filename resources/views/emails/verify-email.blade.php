<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vérifiez votre email</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
    .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: #1a6b6b; padding: 32px 40px; text-align: center; }
    .header h1 { color: #fff; font-size: 22px; margin: 0; letter-spacing: -.3px; }
    .header p { color: rgba(255,255,255,.75); font-size: 13px; margin: 6px 0 0; }
    .body { padding: 36px 40px; }
    .body h2 { font-size: 20px; color: #1a2332; margin: 0 0 12px; }
    .body p { font-size: 14px; color: #5a6a7e; line-height: 1.7; margin: 0 0 20px; }
    .btn { display: block; width: fit-content; margin: 0 auto 24px; background: #1a6b6b; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 9px; font-size: 15px; font-weight: 700; }
    .note { font-size: 12px; color: #9aa5b4; text-align: center; }
    .footer { background: #f4f7f9; padding: 18px 40px; text-align: center; font-size: 12px; color: #9aa5b4; border-top: 1px solid #dde3ea; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🚲 FahrradHauskauf</h1>
      <p>Votre boutique de vélos en ligne</p>
    </div>
    <div class="body">
      <h2>Bonjour {{ $user->name }} 👋</h2>
      <p>Merci de vous être inscrit sur FahrradHauskauf ! Pour activer votre compte, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous.</p>
      <a href="{{ $verificationUrl }}" class="btn">✅ Vérifier mon email</a>
      <p class="note">Ce lien expire dans <strong>24 heures</strong>. Si vous n'avez pas créé de compte, ignorez cet email.</p>
    </div>
    <div class="footer">
      © {{ date('Y') }} FahrradHauskauf — Tous droits réservés
    </div>
  </div>
</body>
</html>