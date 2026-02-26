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
    Schema::create('component_compatibilities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('component_id')->constrained('bike_components')->onDelete('cascade');
        $table->foreignId('compatible_with_id')->constrained('bike_components')->onDelete('cascade');
        $table->timestamps();
        
        // Éviter les doublons
        $table->unique(['component_id', 'compatible_with_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_compatibilities');
    }
};
