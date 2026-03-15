<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Mail\PaymentRejectedMail;

class PaymentProofRejected extends Notification
{
    use Queueable;

    public function __construct(public PaymentProof $proof) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new PaymentRejectedMail($this->proof))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'             => 'payment_rejected',
            'title'            => '❌ Paiement rejeté',
            'message'          => 'Votre preuve de paiement pour la commande ' . $this->proof->order->order_number . ' a été rejetée. Raison : ' . $this->proof->rejection_reason,
            'proof_id'         => $this->proof->id,
            'order_id'         => $this->proof->order->id,
            'order_number'     => $this->proof->order->order_number,
            'rejection_reason' => $this->proof->rejection_reason,
            'url'              => '/orders/' . $this->proof->order->id,
        ];
    }
}