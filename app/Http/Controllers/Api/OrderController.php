<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdmin;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product.primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product.primaryImage', 'items.variant', 'shippingAddress', 'billingAddress', 'coupon'])
            ->findOrFail($id);

        return response()->json($order);
    }

    // ─── Commande depuis le panier ────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id'  => 'required|exists:addresses,id',
            'payment_method'      => 'required|in:card,transfer,bank_transfer,virement',
            'coupon_code'         => 'nullable|string',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->with('items.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Le panier est vide'], 400);
        }

        [$subtotal, $tax, $shippingCost, $discount, $couponId] = $this->computeTotals($cart->items, $request->coupon_code);
        $total = $subtotal + $tax + $shippingCost - $discount;
        $order = $this->createOrder($request, $subtotal, $tax, $shippingCost, $discount, $total, $couponId);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name'       => $item->product->name,
                'product_sku'        => $item->product->sku,
                'price'              => $item->price,
                'quantity'           => $item->quantity,
                'subtotal'           => $item->price * $item->quantity,
                'configuration'      => $item->configuration,
            ]);
            $item->product->decrement('stock', $item->quantity);
        }

        $cart->items()->delete();

        $order->load(['items', 'user', 'shippingAddress', 'billingAddress']);

        return response()->json(['message' => 'Commande créée avec succès', 'order' => $order], 201);
    }

    // ─── Acheter maintenant (produit unique, panier intact) ───────────────────
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id'          => 'required|exists:products,id',
            'quantity'            => 'required|integer|min:1',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id'  => 'required|exists:addresses,id',
            'payment_method'      => 'required|in:card,transfer,bank_transfer,virement',
            'coupon_code'         => 'nullable|string',
            'product_variant_id'  => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stock insuffisant (' . $product->stock . ' disponible(s))'
            ], 400);
        }

        $price        = $product->sale_price ?? $product->price;
        $subtotal     = $price * $request->quantity;
        $tax          = $subtotal * 0.20;
        $shippingCost = 0;
        $discount     = 0;
        $couponId     = null;

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->type === 'percentage'
                    ? $subtotal * ($coupon->value / 100)
                    : $coupon->value;
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }
                $couponId = $coupon->id;
                $coupon->increment('usage_count');
            }
        }

        $total = $subtotal + $tax + $shippingCost - $discount;
        $order = $this->createOrder($request, $subtotal, $tax, $shippingCost, $discount, $total, $couponId);

        $order->items()->create([
            'product_id'         => $product->id,
            'product_variant_id' => $request->product_variant_id,
            'product_name'       => $product->name,
            'product_sku'        => $product->sku,
            'price'              => $price,
            'quantity'           => $request->quantity,
            'subtotal'           => $price * $request->quantity,
            'configuration'      => null,
        ]);

        $product->decrement('stock', $request->quantity);

        $order->load(['items', 'user', 'shippingAddress', 'billingAddress']);

        return response()->json(['message' => 'Commande créée avec succès', 'order' => $order], 201);
    }

    // ─── Annulation ───────────────────────────────────────────────────────────
    public function cancel(Request $request, $id)
    {
        $order = $request->user()->orders()->findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Cette commande ne peut pas être annulée'], 400);
        }

        $order->update(['status' => 'cancelled']);

        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return response()->json(['message' => 'Commande annulée', 'order' => $order]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function computeTotals($items, $couponCode = null): array
    {
        $subtotal     = $items->sum(fn($item) => $item->price * $item->quantity);
        $tax          = $subtotal * 0.20;
        $shippingCost = 0;
        $discount     = 0;
        $couponId     = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->type === 'percentage'
                    ? $subtotal * ($coupon->value / 100)
                    : $coupon->value;
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = $coupon->max_discount;
                }
                $couponId = $coupon->id;
                $coupon->increment('usage_count');
            }
        }

        return [$subtotal, $tax, $shippingCost, $discount, $couponId];
    }

    private function createOrder(Request $request, $subtotal, $tax, $shippingCost, $discount, $total, $couponId): Order
    {
        return Order::create([
            'user_id'             => $request->user()->id,
            'order_number'        => 'ORD-' . strtoupper(Str::random(10)),
            'status'              => 'pending',
            'subtotal'            => $subtotal,
            'tax'                 => $tax,
            'shipping_cost'       => $shippingCost,
            'discount'            => $discount,
            'total'               => $total,
            'coupon_id'           => $couponId,
            'payment_method'      => $request->payment_method,
            'payment_status'      => 'pending',
            'shipping_address_id' => $request->shipping_address_id,
            'billing_address_id'  => $request->billing_address_id,
            'notes'               => $request->notes ?? null,
        ]);
    }

    public function destroy($id)
{
    $order = Order::where('user_id', auth()->id())->findOrFail($id);
    if (!in_array($order->status, ['cancelled', 'delivered'])) {
        return response()->json(['message' => 'Impossible de supprimer une commande en cours.'], 403);
    }
    $order->delete();
    return response()->json(['message' => 'Commande supprimée.']);
}
}
