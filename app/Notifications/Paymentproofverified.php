<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentProofVerified extends Notification
{
    use Queueable;

    public function __construct(public PaymentProof $proof) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->proof->order;
        $url   = config('app.frontend_url') . '/orders/' . $order->id;

        return (new MailMessage)
            ->subject('✅ Paiement confirmé — ' . $order->order_number)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Bonne nouvelle ! Votre preuve de paiement a été **vérifiée et approuvée**.')
            ->line('**Commande :** ' . $order->order_number)
            ->line('**Montant :** ' . number_format($order->total, 2) . ' €')
            ->line('Votre commande est maintenant en cours de traitement. Vous recevrez une confirmation d\'expédition prochainement.')
            ->action('Voir ma commande', $url)
            ->line('Merci pour votre confiance !');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'         => 'payment_proof_verified',
            'proof_id'     => $this->proof->id,
            'order_id'     => $this->proof->order_id,
            'order_number' => $this->proof->order->order_number,
            'message'      => 'Votre paiement pour la commande ' . $this->proof->order->order_number . ' a été confirmé.',
        ];
    }
}