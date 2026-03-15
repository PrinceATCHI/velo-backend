<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentProofSubmitted extends Notification
{
    use Queueable;

    public function __construct(public PaymentProof $proof) {}

    public function via($notifiable): array
    {
        // DB uniquement — plus d'email pour les commandes
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'             => 'payment_proof_submitted',
            'title'            => '💳 Nouvelle preuve de paiement',
            'message'          => $this->proof->user->name . ' a soumis une preuve pour la commande ' . $this->proof->order->order_number,
            'proof_id'         => $this->proof->id,
            'order_id'         => $this->proof->order->id,
            'order_number'     => $this->proof->order->order_number,
            'user_name'        => $this->proof->user->name,
            'amount'           => $this->proof->amount,
            'url'              => '/admin/payment-proofs',
        ];
    }
}