<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('beneficiary_name')->default('FAHRRAD HAUSKAUF SARL');
            $table->string('iban', 100)->default('FR76 1234 5678 9012 3456 7890 123');
            $table->string('bic', 50)->default('BNPAFRPPXXX');
            $table->string('bank_name')->default('BNP PARIBAS');
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insérer les données par défaut
        DB::table('payment_settings')->insert([
            'beneficiary_name' => 'FAHRRAD HAUSKAUF SARL',
            'iban' => 'FR76 1234 5678 9012 3456 7890 123',
            'bic' => 'BNPAFRPPXXX',
            'bank_name' => 'BNP PARIBAS',
            'instructions' => "Effectuez le transfert bancaire depuis votre banque\nMentionnez obligatoirement la référence de commande\nMontant exact\nPrenez une capture d'écran de votre transfert\nSoumettez la preuve de paiement\nVotre commande sera traitée sous 24h après vérification",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};