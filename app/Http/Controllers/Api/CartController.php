<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Récupérer le panier
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        
        $cart->load([
            'items.product.primaryImage',
            'items.variant'
        ]);

        return response()->json([
            'cart' => $cart,
            'total' => $cart->total,
            'items_count' => $cart->items->sum('quantity')
        ]);
    }

    // Ajouter au panier
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'configuration' => 'nullable|array',
        ]);

        $cart = $this->getOrCreateCart($request);
        $product = Product::findOrFail($request->product_id);

        // Vérifier si l'article existe déjà
        $cartItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($cartItem) {
            // Mettre à jour la quantité
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Créer un nouvel article
            $price = $request->product_variant_id 
                ? $product->variants()->find($request->product_variant_id)->price_adjustment + $product->price
                : $product->price;

            $cartItem = $cart->items()->create([
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $price,
                'configuration' => $request->configuration,
            ]);
        }

        return response()->json([
            'message' => 'Produit ajouté au panier',
            'cart_item' => $cartItem->load('product.primaryImage'),
        ], 201);
    }

    // Mettre à jour la quantité
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request);
        $cartItem = $cart->items()->findOrFail($itemId);
        
        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Quantité mise à jour',
            'cart_item' => $cartItem,
        ]);
    }

    // Supprimer un article
    public function destroy(Request $request, $itemId)
    {
        $cart = $this->getOrCreateCart($request);
        $cartItem = $cart->items()->findOrFail($itemId);
        $cartItem->delete();

        return response()->json([
            'message' => 'Article supprimé du panier'
        ]);
    }

    // Vider le panier
    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();

        return response()->json([
            'message' => 'Panier vidé'
        ]);
    }

    // Récupérer ou créer un panier
    private function getOrCreateCart(Request $request)
    {
        if ($request->user()) {
            // Utilisateur connecté
            $cart = Cart::firstOrCreate([
                'user_id' => $request->user()->id
            ]);
        } else {
            // Utilisateur invité (utilise session_id)
            $sessionId = $request->header('X-Session-ID') ?? session()->getId();
            
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }

        return $cart;
    }
}