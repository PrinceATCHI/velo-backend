<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    /**
     * Accepter les CGU
     * POST /api/auth/accept-terms
     */
    public function accept(Request $request)
    {
        $user = $request->user();
        $user->update(['terms_accepted_at' => now()]);

        return response()->json([
            'success'           => true,
            'message'           => 'Conditions acceptées',
            'terms_accepted_at' => $user->terms_accepted_at,
        ]);
    }

    /**
     * Refuser les CGU → supprimer le compte
     * DELETE /api/auth/refuse-terms
     */
    public function refuse(Request $request)
    {
        $user = $request->user();

        // Révoque tous les tokens Sanctum
        $user->tokens()->delete();

        // Supprime définitivement le compte
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compte supprimé',
        ]);
    }

    /**
     * Vérifier le statut d'acceptation des CGU
     * GET /api/auth/terms-status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'terms_accepted'    => !is_null($user->terms_accepted_at),
            'terms_accepted_at' => $user->terms_accepted_at,
        ]);
    }
}