<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'              => 'required|exists:orders,id',
            'transaction_reference' => 'required|string|max:255',
            'transaction_date'      => 'required|date|before_or_equal:today',
            'amount'                => 'required|numeric|min:0',
            // ✅ Supprime 'image' qui bloque les PDF — utilise uniquement mimes + size
            'screenshot'            => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:5120',
            'comment'               => 'nullable|string|max:1000',
        ]);

        // Vérifier que la commande appartient à l'utilisateur connecté
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Bloquer si une preuve est déjà en attente de vérification
        if ($order->paymentProof && $order->paymentProof->status === 'pending') {
            return response()->json([
                'message' => 'Une preuve de paiement est déjà en attente de vérification.',
            ], 422);
        }

        // Upload du fichier
        $file     = $request->file('screenshot');
        $filename = 'payment_proof_' . $order->order_number . '_' . time()
                    . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('payment_proofs', $filename, 'public');

        // Créer la preuve
        $proof = PaymentProof::create([
            'order_id'              => $order->id,
            'user_id'               => auth()->id(),
            'transaction_reference' => $request->transaction_reference,
            'transaction_date'      => $request->transaction_date,
            'amount'                => $request->amount,
            'screenshot_path'       => $path,
            'comment'               => $request->comment,
            'status'                => 'pending',
        ]);

        // Mettre à jour le statut de paiement de la commande
        $order->update(['payment_status' => 'pending_verification']);

        return response()->json([
            'message' => 'Preuve de paiement soumise avec succès.',
            'proof'   => $proof,
        ], 201);
    }

    public function show($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->with('paymentProof')
            ->firstOrFail();

        return response()->json($order->paymentProof);
    }
}