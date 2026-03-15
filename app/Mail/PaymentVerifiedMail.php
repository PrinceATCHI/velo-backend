<?php

namespace App\Mail;

use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PaymentProof $proof) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Paiement confirmé — Commande ' . $this->proof->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-verified');
    }
}