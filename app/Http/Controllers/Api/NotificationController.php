<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ─── Récupérer les notifications de l'utilisateur connecté ───────────────
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin → uniquement les notifs PaymentProof soumises
        // Client → uniquement les notifs PaymentProofVerified/Rejected
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'notifications' => $notifications->items(),
            'unread_count'  => $user->unreadNotifications()->count(),
            'total'         => $notifications->total(),
        ]);
    }

    // ─── Marquer une notification comme lue ──────────────────────────────────
    public function markRead(Request $request, $id)
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification lue']);
    }

    // ─── Marquer toutes comme lues ────────────────────────────────────────────
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'Toutes les notifications lues']);
    }

    // ─── Nombre de notifications non lues (pour le badge) ────────────────────
    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}