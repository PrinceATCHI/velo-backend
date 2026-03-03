<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Liste des commandes de l'utilisateur
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product.primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    // Détails d'une commande
    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with([
                'items.product.primaryImage',
                'items.variant',
                'shippingAddress',
                'billingAddress',
                'coupon'
            ])
            ->findOrFail($id);

        return response()->json($order);
    }

    // Créer une commande
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:card,transfer,bank_transfer,virement',
            'coupon_code' => 'nullable|string',
        ]);

        // Récupérer le panier
        $cart = Cart::where('user_id', $request->user()->id)
            ->with('items.product')
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Le panier est vide'
            ], 400);
        }

        // Calculer le total
        $subtotal = $cart->items->sum(function($item) {
            return $item->price * $item->quantity;
        });

        $tax = $subtotal * 0.20; // TVA 20%
        $shippingCost = 0; // Gratuit ou selon règles
        $discount = 0;
        $couponId = null;

        // Appliquer le coupon si fourni
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            
            if ($coupon && $coupon->isValid()) {
                if ($coupon->type === 'percentage') {
                    $discount = $subtotal * ($coupon->value / 100);
                } else {
                    $discount = $coupon->value;
                }

                // Appliquer max_discount si défini
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }

                $couponId = $coupon->id;
                $coupon->increment('usage_count');
            }
        }

        $total = $subtotal + $tax + $shippingCost - $discount;

        // Créer la commande
        $order = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'coupon_id' => $couponId,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'shipping_address_id' => $request->shipping_address_id,
            'billing_address_id' => $request->billing_address_id,
            'notes' => $request->notes,
        ]);

        // Créer les items de commande
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product->name,
                'product_sku' => $item->product->sku,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->price * $item->quantity,
                'configuration' => $item->configuration,
            ]);

            // Décrémenter le stock
            $item->product->decrement('stock', $item->quantity);
        }

        // Vider le panier
        $cart->items()->delete();

        return response()->json([
            'message' => 'Commande créée avec succès',
            'order' => $order->load(['items', 'shippingAddress', 'billingAddress']),
        ], 201);
    }

    // Annuler une commande
    public function cancel(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Cette commande ne peut pas être annulée'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        // Remettre le stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return response()->json([
            'message' => 'Commande annulée',
            'order' => $order,
        ]);
    }
}