<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nouvelle commande</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
    .wrap { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: #1a6b6b; padding: 28px 40px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .header p { color: rgba(255,255,255,.75); font-size: 13px; margin: 4px 0 0; }
    .body { padding: 32px 40px; }
    .alert { background: #e8f4f4; border-left: 4px solid #1a6b6b; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; font-size: 14px; color: #1a2332; }
    .section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #5a6a7e; margin: 24px 0 10px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
    .info-box { background: #f4f7f9; border-radius: 8px; padding: 12px 16px; }
    .info-box .lbl { font-size: 11px; color: #9aa5b4; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
    .info-box .val { font-size: 14px; color: #1a2332; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th { background: #f4f7f9; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; color: #5a6a7e; }
    tbody td { padding: 11px 12px; border-bottom: 1px solid #dde3ea; color: #1a2332; }
    tbody tr:last-child td { border-bottom: none; }
    .total-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-top: 2px solid #1a6b6b; margin-top: 10px; }
    .total-row .lbl { font-size: 15px; font-weight: 700; color: #1a2332; }
    .total-row .val { font-size: 22px; font-weight: 800; color: #1a6b6b; }
    .btn { display: inline-block; background: #1a6b6b; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 700; margin-top: 20px; }
    .footer { background: #f4f7f9; padding: 16px 40px; text-align: center; font-size: 12px; color: #9aa5b4; border-top: 1px solid #dde3ea; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>🛒 Nouvelle commande reçue</h1>
      <p>{{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="body">
      <div class="alert">
        Commande <strong>#{{ $order->order_number }}</strong> passée par <strong>{{ $order->user->name }}</strong> ({{ $order->user->email }})
      </div>

      <div class="section-title">Récapitulatif</div>
      <div class="info-grid">
        <div class="info-box">
          <div class="lbl">Numéro</div>
          <div class="val">#{{ $order->order_number }}</div>
        </div>
        <div class="info-box">
          <div class="lbl">Paiement</div>
          <div class="val">{{ ucfirst($order->payment_method) }}</div>
        </div>
        <div class="info-box">
          <div class="lbl">Statut</div>
          <div class="val">{{ ucfirst($order->status) }}</div>
        </div>
        <div class="info-box">
          <div class="lbl">Client</div>
          <div class="val">{{ $order->user->name }}</div>
        </div>
      </div>

      <div class="section-title">Articles commandés</div>
      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th>Qté</th>
            <th>Prix unit.</th>
            <th>Sous-total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price, 2) }} €</td>
            <td>{{ number_format($item->subtotal, 2) }} €</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div style="margin-top:12px; font-size:13px; color:#5a6a7e; text-align:right;">
        <div>Sous-total HT : <strong>{{ number_format($order->subtotal, 2) }} €</strong></div>
        <div>TVA (20%) : <strong>{{ number_format($order->tax, 2) }} €</strong></div>
        @if($order->discount > 0)
        <div>Remise : <strong>-{{ number_format($order->discount, 2) }} €</strong></div>
        @endif
      </div>
      <div class="total-row">
        <span class="lbl">Total TTC</span>
        <span class="val">{{ number_format($order->total, 2) }} €</span>
      </div>

      <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}" class="btn">
        👁 Voir la commande dans l'admin
      </a>
    </div>
    <div class="footer">
      © {{ date('Y') }} FahrradHauskauf — Notification automatique
    </div>
  </div>
</body>
</html>