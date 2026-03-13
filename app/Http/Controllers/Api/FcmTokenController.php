<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    // Enregistrer ou mettre à jour le token FCM du device
    public function store(Request $request)
    {
        $request->validate([
            'token'       => 'required|string',
            'device_type' => 'nullable|in:web,android,ios',
        ]);

        FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id'     => auth()->id(),
                'device_type' => $request->device_type ?? 'web',
            ]
        );

        return response()->json(['message' => 'Token FCM enregistré']);
    }

    // Supprimer le token (déconnexion)
    public function destroy(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        FcmToken::where('token', $request->token)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['message' => 'Token FCM supprimé']);
    }
}