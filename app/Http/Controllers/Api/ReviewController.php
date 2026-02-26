<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Liste des avis d'un produit
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    // Créer un avis
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        // Vérifier si l'utilisateur a déjà laissé un avis
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour ce produit'
            ], 400);
        }

        // Vérifier si achat vérifié
        $isVerifiedPurchase = false;
        if ($request->order_id) {
            $order = $request->user()->orders()
                ->where('id', $request->order_id)
                ->whereHas('items', function($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                })
                ->first();

            $isVerifiedPurchase = $order ? true : false;
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => true, // Auto-approuvé, ou mettre false pour modération
        ]);

        return response()->json([
            'message' => 'Avis publié avec succès',
            'review' => $review->load('user'),
        ], 201);
    }

    // Mettre à jour un avis
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10',
        ]);

        $review = Review::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Avis mis à jour',
            'review' => $review,
        ]);
    }

    // Supprimer un avis
    public function destroy(Request $request, $id)
    {
        $review = Review::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $review->delete();

        return response()->json([
            'message' => 'Avis supprimé'
        ]);
    }
}