<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Notifications\PaymentProofVerified;
use App\Notifications\PaymentProofRejected;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    // Liste toutes les preuves de paiement
    public function index(Request $request)
    {
        $query = PaymentProof::with(['order', 'user']);

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

    // ✅ Approuver une preuve
    public function verify(Request $request, $id)
    {
        $proof = PaymentProof::with(['order', 'user'])->findOrFail($id);

        $proof->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Mettre à jour le statut de la commande
        $proof->order->update([
            'payment_status' => 'paid',
            'status'         => 'processing',
        ]);

        // ✅ Notifier le client par email
        $proof->user->notify(new PaymentProofVerified($proof));

        return response()->json([
            'message' => 'Preuve de paiement vérifiée',
            'proof'   => $proof->fresh(),
        ]);
    }

    // ✅ Rejeter une preuve
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $proof = PaymentProof::with(['order', 'user'])->findOrFail($id);

        $proof->update([
            'status'           => 'rejected',
            'verified_by'      => auth()->id(),
            'verified_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Mettre à jour le statut de la commande
        $proof->order->update(['payment_status' => 'failed']);

        // ✅ Notifier le client par email
        $proof->user->notify(new PaymentProofRejected($proof));

        return response()->json([
            'message' => 'Preuve de paiement rejetée',
            'proof'   => $proof->fresh(),
        ]);
    }

    // Lire les notifications admin (panel)
    public function notifications(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->where('type', 'like', '%PaymentProof%')
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    // Marquer une notification comme lue
    public function markRead(Request $request, $notificationId)
    {
        $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    // Marquer toutes comme lues
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()
            ->where('type', 'like', '%PaymentProof%')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
    }
    public function destroy($id)
  {
    $proof = \App\Models\PaymentProof::findOrFail($id);
    $proof->delete();
    return response()->json(['message' => 'Preuve supprimée.']);

}

}