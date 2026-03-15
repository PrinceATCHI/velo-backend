<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Mail\PaymentVerifiedMail;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentProofVerified extends Notification
{
    use Queueable;

    public function __construct(public PaymentProof $proof) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new PaymentVerifiedMail($this->proof))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'payment_verified',
            'title'        => '✅ Paiement confirmé',
            'message'      => 'Votre paiement pour la commande ' . $this->proof->order->order_number . ' a été validé. Votre commande est en cours de traitement.',
            'proof_id'     => $this->proof->id,
            'order_id'     => $this->proof->order->id,
            'order_number' => $this->proof->order->order_number,
            'url'          => '/orders/' . $this->proof->order->id,
        ];
    }
}