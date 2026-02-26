<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Profil utilisateur
    public function profile(Request $request)
    {
        $user = $request->user()->load(['addresses', 'orders']);
        
        return response()->json($user);
    }

    // Mettre à jour le profil
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Profil mis à jour',
            'user' => $request->user(),
        ]);
    }

    // Changer le mot de passe
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect'
            ], 400);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Mot de passe mis à jour'
        ]);
    }

    // Liste des adresses
    public function addresses(Request $request)
    {
        $addresses = $request->user()->addresses;
        
        return response()->json($addresses);
    }

    // Ajouter une adresse
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'is_default' => 'boolean',
        ]);

        // Si nouvelle adresse par défaut, retirer le défaut des autres
        if ($request->is_default) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($request->all());

        return response()->json([
            'message' => 'Adresse ajoutée',
            'address' => $address,
        ], 201);
    }

    // Mettre à jour une adresse
    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $address = $request->user()->addresses()->findOrFail($id);

        if ($request->is_default) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json([
            'message' => 'Adresse mise à jour',
            'address' => $address,
        ]);
    }

    // Supprimer une adresse
    public function destroyAddress(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json([
            'message' => 'Adresse supprimée'
        ]);
    }
}