<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BikeComponent;
use App\Models\BikeConfiguration;
use Illuminate\Http\Request;

class BikeConfiguratorController extends Controller
{
    // Liste des composants par type
    public function getComponents($type = null)
    {
        $query = BikeComponent::where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        $components = $query->orderBy('name')->get();

        return response()->json($components);
    }

    // Composants compatibles
    public function getCompatibleComponents($componentId)
    {
        $component = BikeComponent::findOrFail($componentId);
        
        $compatible = $component->compatibleWith()
            ->where('is_active', true)
            ->get();

        return response()->json($compatible);
    }

    // Vérifier la compatibilité
    public function checkCompatibility(Request $request)
    {
        $request->validate([
            'component_ids' => 'required|array',
            'component_ids.*' => 'exists:bike_components,id',
        ]);

        $components = BikeComponent::whereIn('id', $request->component_ids)->get();
        $incompatibilities = [];

        // Vérifier les incompatibilités
        foreach ($components as $component) {
            $compatibleIds = $component->compatibleWith()->pluck('id')->toArray();
            
            foreach ($components as $otherComponent) {
                if ($component->id !== $otherComponent->id) {
                    if (!empty($compatibleIds) && !in_array($otherComponent->id, $compatibleIds)) {
                        $incompatibilities[] = [
                            'component_1' => $component->name,
                            'component_2' => $otherComponent->name,
                            'message' => "{$component->name} n'est pas compatible avec {$otherComponent->name}"
                        ];
                    }
                }
            }
        }

        return response()->json([
            'is_compatible' => empty($incompatibilities),
            'incompatibilities' => $incompatibilities,
        ]);
    }

    // Calculer le prix total
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'component_ids' => 'required|array',
            'component_ids.*' => 'exists:bike_components,id',
        ]);

        $components = BikeComponent::whereIn('id', $request->component_ids)->get();
        $totalPrice = $components->sum('price');

        return response()->json([
            'components' => $components,
            'total_price' => $totalPrice,
        ]);
    }

    // Sauvegarder une configuration
    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'component_ids' => 'required|array',
            'component_ids.*' => 'exists:bike_components,id',
        ]);

        $components = BikeComponent::whereIn('id', $request->component_ids)->get();
        $totalPrice = $components->sum('price');

        $configuration = BikeConfiguration::create([
            'user_id' => $request->user()->id,
            'name' => $request->name ?? 'Ma configuration ' . now()->format('d/m/Y'),
            'components' => $request->component_ids,
            'total_price' => $totalPrice,
            'is_saved' => true,
        ]);

        return response()->json([
            'message' => 'Configuration sauvegardée',
            'configuration' => $configuration,
        ], 201);
    }

    // Liste des configurations sauvegardées
    public function myConfigurations(Request $request)
    {
        $configurations = $request->user()
            ->bikeConfigurations()
            ->where('is_saved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Charger les composants pour chaque config
        foreach ($configurations as $config) {
            $config->components_list = $config->getComponentsList();
        }

        return response()->json($configurations);
    }

    // Détails d'une configuration
    public function showConfiguration(Request $request, $id)
    {
        $configuration = $request->user()
            ->bikeConfigurations()
            ->findOrFail($id);

        $configuration->components_list = $configuration->getComponentsList();

        return response()->json($configuration);
    }

    // Supprimer une configuration
    public function deleteConfiguration(Request $request, $id)
    {
        $configuration = $request->user()
            ->bikeConfigurations()
            ->findOrFail($id);

        $configuration->delete();

        return response()->json([
            'message' => 'Configuration supprimée'
        ]);
    }
}