<?php

namespace App\Notifications;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentProofSubmitted extends Notification
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
        $user   = $this->proof->user;
        $url    = config('app.frontend_url') . '/admin/payment-proofs';

        return (new MailMessage)
            ->subject('💳 Nouvelle preuve de paiement — ' . $order->order_number)
            ->greeting('Bonjour Admin,')
            ->line('Un client vient de soumettre une preuve de virement bancaire.')
            ->line('**Client :** ' . $user->name . ' (' . $user->email . ')')
            ->line('**Commande :** ' . $order->order_number)
            ->line('**Montant déclaré :** ' . number_format($this->proof->amount, 2) . ' €')
            ->line('**Référence transaction :** ' . $this->proof->transaction_reference)
            ->line('**Date du virement :** ' . $this->proof->transaction_date)
            ->action('Vérifier la preuve', $url)
            ->line('Merci de vérifier et valider ou rejeter cette preuve rapidement.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'                   => 'payment_proof_submitted',
            'proof_id'               => $this->proof->id,
            'order_id'               => $this->proof->order_id,
            'order_number'           => $this->proof->order->order_number,
            'user_name'              => $this->proof->user->name,
            'user_email'             => $this->proof->user->email,
            'amount'                 => $this->proof->amount,
            'transaction_reference'  => $this->proof->transaction_reference,
            'message'                => 'Nouvelle preuve de paiement soumise par ' . $this->proof->user->name,
        ];
    }
}