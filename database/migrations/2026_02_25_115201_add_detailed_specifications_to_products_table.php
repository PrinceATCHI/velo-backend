<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Version corrigée — vérifie chaque colonne avant de l'ajouter.
 * Remplace database/migrations/2026_02_25_115201_add_detailed_specifications_to_products_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // ── Identité ──
            if (!Schema::hasColumn('products', 'bike_type'))        $table->string('bike_type', 100)->nullable()->after('category_id');
            if (!Schema::hasColumn('products', 'age_range'))        $table->string('age_range', 50)->nullable();
            if (!Schema::hasColumn('products', 'model_name'))       $table->string('model_name')->nullable();
            if (!Schema::hasColumn('products', 'year'))             $table->year('year')->nullable();
            if (!Schema::hasColumn('products', 'brand'))            $table->string('brand')->nullable();
            if (!Schema::hasColumn('products', 'sku'))              $table->string('sku')->nullable();

            // ── Roues & cadre ──
            if (!Schema::hasColumn('products', 'wheel_size'))       $table->string('wheel_size', 20)->nullable();
            if (!Schema::hasColumn('products', 'wheel_width'))      $table->string('wheel_width', 20)->nullable();
            if (!Schema::hasColumn('products', 'wheel_material'))   $table->string('wheel_material')->nullable();
            if (!Schema::hasColumn('products', 'frame_material'))   $table->string('frame_material')->nullable();

            // ── Transmission & freinage ──
            if (!Schema::hasColumn('products', 'number_of_speeds')) $table->string('number_of_speeds', 50)->nullable();
            if (!Schema::hasColumn('products', 'suspension_type'))  $table->string('suspension_type')->nullable();
            if (!Schema::hasColumn('products', 'brake_style'))      $table->string('brake_style')->nullable();

            // ── Électrique ──
            if (!Schema::hasColumn('products', 'power_source'))           $table->string('power_source')->nullable();
            if (!Schema::hasColumn('products', 'motor_power'))            $table->string('motor_power')->nullable();
            if (!Schema::hasColumn('products', 'battery_capacity'))       $table->string('battery_capacity')->nullable();
            if (!Schema::hasColumn('products', 'battery_energy_content')) $table->string('battery_energy_content')->nullable();
            if (!Schema::hasColumn('products', 'max_speed'))              $table->string('max_speed', 50)->nullable();
            if (!Schema::hasColumn('products', 'max_range'))              $table->string('max_range', 50)->nullable();
            if (!Schema::hasColumn('products', 'charging_time'))          $table->string('charging_time', 50)->nullable();

            // ── Dimensions & poids ──
            if (!Schema::hasColumn('products', 'weight'))         $table->string('weight', 50)->nullable();
            if (!Schema::hasColumn('products', 'max_load'))       $table->string('max_load', 50)->nullable();
            if (!Schema::hasColumn('products', 'dimensions'))     $table->string('dimensions')->nullable();
            if (!Schema::hasColumn('products', 'package_weight')) $table->string('package_weight', 50)->nullable();

            // ── Certifications & garantie ──
            if (!Schema::hasColumn('products', 'waterproof_rating'))     $table->string('waterproof_rating', 20)->nullable();
            if (!Schema::hasColumn('products', 'certification'))         $table->string('certification')->nullable();
            if (!Schema::hasColumn('products', 'warranty_type'))         $table->string('warranty_type', 50)->nullable();
            if (!Schema::hasColumn('products', 'warranty_description'))  $table->text('warranty_description')->nullable();
            if (!Schema::hasColumn('products', 'included_components'))   $table->text('included_components')->nullable();
            if (!Schema::hasColumn('products', 'special_features'))      $table->text('special_features')->nullable();
            if (!Schema::hasColumn('products', 'special_features_de'))   $table->text('special_features_de')->nullable();
            if (!Schema::hasColumn('products', 'assembly_required'))     $table->boolean('assembly_required')->default(false);

            // ── Flags ──
            if (!Schema::hasColumn('products', 'is_new'))      $table->boolean('is_new')->default(false);
            if (!Schema::hasColumn('products', 'is_active'))   $table->boolean('is_active')->default(true);

            // ── Multilingue ──
            if (!Schema::hasColumn('products', 'name_de'))           $table->string('name_de')->nullable();
            if (!Schema::hasColumn('products', 'description_de'))    $table->text('description_de')->nullable();

            // ── Variants ──
            if (!Schema::hasColumn('products', 'colors')) $table->string('colors')->nullable();
            if (!Schema::hasColumn('products', 'sizes'))  $table->string('sizes')->nullable();
            if (!Schema::hasColumn('products', 'color'))  $table->string('color')->nullable();
        });
    }

    public function down(): void
    {
        // Ne supprime rien pour éviter de perdre des données
    }
};