<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // ── Noms & descriptions (FR + DE) ──
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_de')->nullable();

            // ── Prix & stock ──
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);

            // ── Flags ──
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('assembly_required')->default(false);

            // ── Marque & identification ──
            $table->string('brand')->nullable();
            $table->string('model_name')->nullable();
            $table->string('year')->nullable();

            // ── Type & usage ──
            $table->string('bike_type')->nullable();       // ex: Hardtail, Tout-suspendu, Gravel…
            $table->string('age_range')->nullable();       // ex: 6–10 ans
            $table->string('color')->nullable();
            $table->string('colors')->nullable();          // JSON ou virgule-séparé
            $table->string('sizes')->nullable();           // JSON ou virgule-séparé

            // ── Roues & cadre ──
            $table->string('wheel_size')->nullable();      // ex: 29", 700c, 27.5"
            $table->string('wheel_width')->nullable();
            $table->string('wheel_material')->nullable();
            $table->string('frame_material')->nullable();  // ex: Alu 6061, Carbone FACT

            // ── Transmission & freinage ──
            $table->string('number_of_speeds')->nullable();
            $table->string('suspension_type')->nullable();
            $table->string('brake_style')->nullable();

            // ── Électrique / batterie ──
            $table->string('power_source')->nullable();
            $table->string('motor_power')->nullable();
            $table->string('battery_capacity')->nullable();
            $table->string('battery_energy_content')->nullable();
            $table->string('max_speed')->nullable();
            $table->string('max_range')->nullable();
            $table->string('charging_time')->nullable();

            // ── Dimensions & poids ──
            $table->string('weight')->nullable();
            $table->string('max_load')->nullable();
            $table->string('dimensions')->nullable();

            // ── Certifications & garantie ──
            $table->string('certification')->nullable();
            $table->string('waterproof_rating')->nullable();
            $table->string('warranty_type')->nullable();
            $table->text('warranty_description')->nullable();

            // ── Extras ──
            $table->text('included_components')->nullable();
            $table->text('special_features')->nullable();
            $table->text('special_features_de')->nullable();

            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->text('image_path');      // URL Unsplash OU chemin storage local
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
