<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // Liste de souhaits
    public function index(Request $request)
    {
        $wishlist = $request->user()
            ->wishlists()
            ->with('product.primaryImage')
            ->get();

        return response()->json($wishlist);
    }

    // Ajouter à la wishlist
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Vérifier si déjà dans la wishlist
        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce produit est déjà dans votre liste de souhaits'
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => 'Produit ajouté à la liste de souhaits',
            'wishlist' => $wishlist->load('product.primaryImage'),
        ], 201);
    }

    // Retirer de la wishlist
    public function destroy(Request $request, $productId)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $wishlist->delete();

        return response()->json([
            'message' => 'Produit retiré de la liste de souhaits'
        ]);
    }
}