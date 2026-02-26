<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Liste toutes les commandes
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Recherche par numéro de commande
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($orders);
    }

    // Détails d'une commande
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'shippingAddress',
            'billingAddress',
            'coupon'
        ])->findOrFail($id);

        return response()->json($order);
    }

    // Mettre à jour le statut
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());

        // Mettre à jour les dates selon le statut
        if ($request->status === 'shipped' && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }
        if ($request->status === 'delivered' && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }
        if ($request->payment_status === 'paid' && !$order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return response()->json([
            'message' => 'Commande mise à jour',
            'order' => $order,
        ]);
    }

    // Supprimer une commande
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'cancelled') {
            return response()->json([
                'message' => 'Seules les commandes annulées peuvent être supprimées'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'message' => 'Commande supprimée'
        ]);
    }
}