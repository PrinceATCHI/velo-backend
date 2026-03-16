<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Code de vérification</title>
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f4f7f9;margin:0;padding:0}
    .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}
    .header{background:#1a6b6b;padding:32px 40px;text-align:center}
    .header h1{color:#fff;font-size:22px;margin:0;letter-spacing:-.3px}
    .header p{color:rgba(255,255,255,.75);font-size:13px;margin:6px 0 0}
    .body{padding:36px 40px;text-align:center}
    .body h2{font-size:20px;color:#1a2332;margin:0 0 12px;text-align:left}
    .body p{font-size:14px;color:#5a6a7e;line-height:1.7;margin:0 0 24px;text-align:left}
    .code-box{background:#e8f4f4;border:2px dashed #1a6b6b;border-radius:12px;padding:24px;margin:0 auto 24px;display:inline-block;min-width:200px}
    .code{font-family:'Courier New',monospace;font-size:42px;font-weight:900;color:#1a6b6b;letter-spacing:12px;display:block}
    .code-label{font-size:12px;color:#5a6a7e;margin-top:8px;display:block}
    .warning{background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;text-align:left;margin-bottom:20px}
    .note{font-size:12px;color:#9aa5b4;text-align:center}
    .footer{background:#f4f7f9;padding:18px 40px;text-align:center;font-size:12px;color:#9aa5b4;border-top:1px solid #dde3ea}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🚲 FahrradHauskauf</h1>
      <p>Vérification de votre adresse email</p>
    </div>
    <div class="body">
      <h2>Bonjour {{ $user->name }} 👋</h2>
      <p>Merci de vous être inscrit sur FahrradHauskauf ! Utilisez le code ci-dessous pour vérifier votre adresse email.</p>
      <div class="code-box">
        <span class="code">{{ $code }}</span>
        <span class="code-label">Code de vérification</span>
      </div>
      <div class="warning">
        ⏰ Ce code expire dans <strong>15 minutes</strong>. Ne le partagez avec personne.
      </div>
      <p class="note">Si vous n'avez pas créé de compte sur FahrradHauskauf, ignorez cet email.</p>
    </div>
    <div class="footer">© {{ date('Y') }} FahrradHauskauf — Tous droits réservés</div>
  </div>
</body>
</html>