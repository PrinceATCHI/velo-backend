<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Paiement rejeté</title>
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#f4f7f9;margin:0;padding:0}
    .wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}
    .header{background:#1a6b6b;padding:32px 40px;text-align:center}
    .header h1{color:#fff;font-size:22px;margin:0}
    .header p{color:rgba(255,255,255,.75);font-size:13px;margin:6px 0 0}
    .body{padding:36px 40px}
    .body h2{font-size:20px;color:#1a2332;margin:0 0 12px}
    .body p{font-size:14px;color:#5a6a7e;line-height:1.7;margin:0 0 16px}
    .error-box{background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:18px;margin-bottom:20px;text-align:center}
    .error-box .icon{font-size:40px;margin-bottom:8px}
    .error-box h3{font-size:17px;font-weight:800;color:#e53935;margin:0 0 4px}
    .error-box p{font-size:13px;color:#5a6a7e;margin:0}
    .reason-box{background:#fff8e1;border:1px solid #f59e0b;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#92400e}
    .reason-box strong{display:block;margin-bottom:4px;font-weight:800}
    .info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #dde3ea;font-size:13px}
    .info-row:last-child{border-bottom:none}
    .info-row .lbl{color:#5a6a7e}
    .info-row .val{font-weight:700;color:#1a2332}
    .btn{display:block;width:fit-content;margin:20px auto 0;background:#1a6b6b;color:#fff!important;text-decoration:none;padding:13px 30px;border-radius:9px;font-size:14px;font-weight:700}
    .footer{background:#f4f7f9;padding:18px 40px;text-align:center;font-size:12px;color:#9aa5b4;border-top:1px solid #dde3ea}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🚲 FahrradHauskauf</h1>
      <p>Statut de votre paiement</p>
    </div>
    <div class="body">
      <h2>Bonjour {{ $proof->user->name }},</h2>
      <div class="error-box">
        <div class="icon">❌</div>
        <h3>Preuve de paiement rejetée</h3>
        <p>Votre preuve de paiement n'a pas pu être validée.</p>
      </div>
      @if($proof->rejection_reason)
      <div class="reason-box">
        <strong>Raison du rejet :</strong>
        {{ $proof->rejection_reason }}
      </div>
      @endif
      <div class="info-row"><span class="lbl">Commande</span><span class="val">#{{ $proof->order->order_number }}</span></div>
      <div class="info-row"><span class="lbl">Montant soumis</span><span class="val">{{ number_format($proof->amount, 2) }} €</span></div>
      <div class="info-row"><span class="lbl">Référence</span><span class="val">{{ $proof->transaction_reference }}</span></div>
      <p style="margin-top:16px">Vous pouvez soumettre une nouvelle preuve de paiement depuis votre espace commandes.</p>
      <a href="{{ config('app.frontend_url') }}/orders/{{ $proof->order->id }}" class="btn">Voir ma commande →</a>
    </div>
    <div class="footer">© {{ date('Y') }} FahrradHauskauf — Tous droits réservés</div>
  </div>
</body>
</html>