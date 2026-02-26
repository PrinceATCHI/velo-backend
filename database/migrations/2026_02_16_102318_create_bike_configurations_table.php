<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('bike_configurations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('name')->nullable(); // Nom de la config
        $table->json('components'); // IDs des composants sélectionnés
        $table->decimal('total_price', 10, 2);
        $table->string('preview_image')->nullable(); // Image 3D générée
        $table->boolean('is_saved')->default(false);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bike_configurations');
    }
};
