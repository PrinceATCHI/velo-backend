<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('orders')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $customers]);
    }

    public function show(string $id)
    {
        $customer = User::with(['orders.items.product'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->findOrFail($id);

        return response()->json($customer);
    }

    public function destroy(string $id)
    {
        $customer = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->findOrFail($id);

        $customer->delete();

        return response()->json(['message' => 'Client supprimé']);
    }
}