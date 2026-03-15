<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentProofRejected extends Notification
{
    use Queueable;

    public function __construct(public PaymentProof $proof) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order  = $this->proof->order;
        $url    = config('app.frontend_url') . '/submit-payment-proof/' . $order->id;
        $reason = $this->proof->rejection_reason ?? 'Non précisé';

        return (new MailMessage)
            ->subject('❌ Preuve de paiement rejetée — ' . $order->order_number)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre preuve de paiement n\'a pas pu être validée.')
            ->line('**Commande :** ' . $order->order_number)
            ->line('**Motif du rejet :** ' . $reason)
            ->line('Merci de soumettre une nouvelle preuve en vous assurant que :')
            ->line('• L\'image est lisible et complète')
            ->line('• La référence de transaction est correcte')
            ->line('• Le montant correspond exactement au total de la commande')
            ->action('Soumettre une nouvelle preuve', $url)
            ->line('Pour toute question, contactez-nous à support@fahrradhauskauf.com');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'             => 'payment_proof_rejected',
            'proof_id'         => $this->proof->id,
            'order_id'         => $this->proof->order_id,
            'order_number'     => $this->proof->order->order_number,
            'rejection_reason' => $this->proof->rejection_reason,
            'message'          => 'Votre preuve de paiement pour la commande ' . $this->proof->order->order_number . ' a été rejetée.',
        ];
    }
}