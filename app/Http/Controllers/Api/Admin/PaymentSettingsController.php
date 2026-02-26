 <?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    /**
     * Récupérer les paramètres de paiement
     */
    public function index()
    {
        $settings = PaymentSettings::first();
        
        if (!$settings) {
            $settings = PaymentSettings::create([
                'beneficiary_name' => 'FAHRRAD HAUSKAUF SARL',
                'iban' => 'FR76 1234 5678 9012 3456 7890 123',
                'bic' => 'BNPAFRPPXXX',
                'bank_name' => 'BNP PARIBAS',
                'is_active' => true,
            ]);
        }

        return response()->json($settings);
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_name' => 'required|string|max:255',
            'iban' => 'required|string|max:100',
            'bic' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $settings = PaymentSettings::first();
        
        if (!$settings) {
            $settings = PaymentSettings::create($validated);
        } else {
            $settings->update($validated);
        }

        return response()->json([
            'message' => 'Paramètres de paiement mis à jour avec succès',
            'settings' => $settings
        ]);
    }
}