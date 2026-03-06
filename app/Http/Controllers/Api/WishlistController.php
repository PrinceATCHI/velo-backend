<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * GET /wishlist
     * Retourne tous les produits en favoris de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();
        $items = $user->wishlistProducts()
            ->with(['images', 'category'])
            ->get();

        return response()->json($items);
    }

    /**
     * POST /wishlist
     * Ajoute un produit aux favoris
     */
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $user = Auth::user();

        // Évite les doublons
        if (!$user->wishlistProducts()->where('product_id', $request->product_id)->exists()) {
            $user->wishlistProducts()->attach($request->product_id);
        }

        return response()->json(['message' => 'Added to wishlist'], 201);
    }

    /**
     * DELETE /wishlist/{productId}
     * Retire un produit des favoris
     */
    public function destroy($productId)
    {
        $user = Auth::user();
        $user->wishlistProducts()->detach($productId);

        return response()->json(['message' => 'Removed from wishlist']);
    }

    /**
     * POST /wishlist/sync
     * Sync localStorage → BDD après connexion
     * Body: { product_ids: [1, 2, 3] }
     */
    public function sync(Request $request)
    {
        $request->validate([
            'product_ids'   => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $user = Auth::user();

        // syncWithoutDetaching = ajoute sans supprimer les existants
        $user->wishlistProducts()->syncWithoutDetaching($request->product_ids);

        return response()->json(['message' => 'Wishlist synced']);
    }
}