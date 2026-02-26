<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    // Soumettre une preuve de paiement
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_reference' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'comment' => 'nullable|string',
        ]);

        // Vérifier que la commande appartient à l'utilisateur
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Vérifier qu'il n'y a pas déjà une preuve en attente
        if ($order->paymentProof && $order->paymentProof->status === 'pending') {
            return response()->json([
                'message' => 'Une preuve de paiement est déjà en attente de vérification'
            ], 422);
        }

        // Upload du screenshot
        $screenshot = $request->file('screenshot');
        $filename = 'payment_proof_' . $order->order_number . '_' . time() . '.' . $screenshot->getClientOriginalExtension();
        $path = $screenshot->storeAs('payment_proofs', $filename, 'public');

        // Créer la preuve de paiement
        $proof = PaymentProof::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'transaction_reference' => $request->transaction_reference,
            'transaction_date' => $request->transaction_date,
            'amount' => $request->amount,
            'screenshot_path' => $path,
            'comment' => $request->comment,
        ]);

        // Mettre à jour le statut de la commande
        $order->update(['payment_status' => 'pending_verification']);

        return response()->json([
            'message' => 'Preuve de paiement soumise avec succès',
            'proof' => $proof,
        ], 201);
    }

    // Voir la preuve de paiement pour une commande
    public function show($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->with('paymentProof')
            ->firstOrFail();

        return response()->json($order->paymentProof);
    }
}