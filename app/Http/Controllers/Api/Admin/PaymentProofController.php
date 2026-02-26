<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    // Liste toutes les preuves de paiement
    public function index(Request $request)
    {
        $query = PaymentProof::with(['order', 'user']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $proofs = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($proofs);
    }

    // Voir une preuve spécifique
    public function show($id)
    {
        $proof = PaymentProof::with(['order', 'user', 'verifier'])->findOrFail($id);

        return response()->json($proof);
    }

    // Vérifier/Approuver une preuve
    public function verify(Request $request, $id)
    {
        $proof = PaymentProof::findOrFail($id);

        $proof->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Mettre à jour le statut de la commande
        $proof->order->update([
            'payment_status' => 'verified',
            'status' => 'processing',
        ]);

        return response()->json([
            'message' => 'Preuve de paiement vérifiée',
            'proof' => $proof,
        ]);
    }

    // Rejeter une preuve
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $proof = PaymentProof::findOrFail($id);

        $proof->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Mettre à jour le statut de la commande
        $proof->order->update(['payment_status' => 'rejected']);

        return response()->json([
            'message' => 'Preuve de paiement rejetée',
            'proof' => $proof,
        ]);
    }
}