<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // Liste des coupons
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return response()->json($coupons);
    }

    // Créer un coupon
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $coupon = Coupon::create($request->all());

        return response()->json([
            'message' => 'Coupon créé avec succès',
            'coupon' => $coupon,
        ], 201);
    }

    // Afficher un coupon
    public function show($id)
    {
        $coupon = Coupon::with('orders')->findOrFail($id);
        return response()->json($coupon);
    }

    // Mettre à jour un coupon
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $coupon->update($request->all());

        return response()->json([
            'message' => 'Coupon mis à jour',
            'coupon' => $coupon,
        ]);
    }

    // Supprimer un coupon
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon supprimé'
        ]);
    }
}