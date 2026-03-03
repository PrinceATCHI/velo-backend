<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration de CORRECTION — ajoute les colonnes manquantes
 * sans toucher aux données existantes.
 *
 * À lancer avec : php artisan migrate
 *
 * Si tu préfères repartir de zéro (BDD vide) :
 *   php artisan migrate:fresh && php artisan db:seed --class=ProductSeeder
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── TABLE categories ──────────────────────────────────────────
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'name_de')) {
                $table->string('name_de')->nullable()->after('name');
            }
            if (!Schema::hasColumn('categories', 'description_de')) {
                $table->text('description_de')->nullable()->after('description');
            }
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // ── TABLE products ────────────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            // Noms & descriptions bilingues
            if (!Schema::hasColumn('products', 'name_de')) {
                $table->string('name_de')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'description_de')) {
                $table->text('description_de')->nullable()->after('description');
            }

            // Flags
            if (!Schema::hasColumn('products', 'is_new')) {
                $table->boolean('is_new')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('products', 'assembly_required')) {
                $table->boolean('assembly_required')->default(false);
            }

            // Marque & identification
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('products', 'model_name')) {
                $table->string('model_name')->nullable();
            }
            if (!Schema::hasColumn('products', 'year')) {
                $table->string('year')->nullable();
            }

            // Type & usage
            if (!Schema::hasColumn('products', 'bike_type')) {
                $table->string('bike_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'age_range')) {
                $table->string('age_range')->nullable();
            }
            if (!Schema::hasColumn('products', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('products', 'colors')) {
                $table->string('colors')->nullable();
            }
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->string('sizes')->nullable();
            }

            // Roues & cadre
            if (!Schema::hasColumn('products', 'wheel_size')) {
                $table->string('wheel_size')->nullable();
            }
            if (!Schema::hasColumn('products', 'wheel_width')) {
                $table->string('wheel_width')->nullable();
            }
            if (!Schema::hasColumn('products', 'wheel_material')) {
                $table->string('wheel_material')->nullable();
            }
            if (!Schema::hasColumn('products', 'frame_material')) {
                $table->string('frame_material')->nullable();
            }

            // Transmission & freinage
            if (!Schema::hasColumn('products', 'number_of_speeds')) {
                $table->string('number_of_speeds')->nullable();
            }
            if (!Schema::hasColumn('products', 'suspension_type')) {
                $table->string('suspension_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'brake_style')) {
                $table->string('brake_style')->nullable();
            }

            // Électrique / batterie
            if (!Schema::hasColumn('products', 'power_source')) {
                $table->string('power_source')->nullable();
            }
            if (!Schema::hasColumn('products', 'motor_power')) {
                $table->string('motor_power')->nullable();
            }
            if (!Schema::hasColumn('products', 'battery_capacity')) {
                $table->string('battery_capacity')->nullable();
            }
            if (!Schema::hasColumn('products', 'battery_energy_content')) {
                $table->string('battery_energy_content')->nullable();
            }
            if (!Schema::hasColumn('products', 'max_speed')) {
                $table->string('max_speed')->nullable();
            }
            if (!Schema::hasColumn('products', 'max_range')) {
                $table->string('max_range')->nullable();
            }
            if (!Schema::hasColumn('products', 'charging_time')) {
                $table->string('charging_time')->nullable();
            }

            // Dimensions & poids
            if (!Schema::hasColumn('products', 'weight')) {
                $table->string('weight')->nullable();
            }
            if (!Schema::hasColumn('products', 'max_load')) {
                $table->string('max_load')->nullable();
            }
            if (!Schema::hasColumn('products', 'dimensions')) {
                $table->string('dimensions')->nullable();
            }

            // Certifications & garantie
            if (!Schema::hasColumn('products', 'certification')) {
                $table->string('certification')->nullable();
            }
            if (!Schema::hasColumn('products', 'waterproof_rating')) {
                $table->string('waterproof_rating')->nullable();
            }
            if (!Schema::hasColumn('products', 'warranty_type')) {
                $table->string('warranty_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'warranty_description')) {
                $table->text('warranty_description')->nullable();
            }

            // Extras
            if (!Schema::hasColumn('products', 'included_components')) {
                $table->text('included_components')->nullable();
            }
            if (!Schema::hasColumn('products', 'special_features')) {
                $table->text('special_features')->nullable();
            }
            if (!Schema::hasColumn('products', 'special_features_de')) {
                $table->text('special_features_de')->nullable();
            }
        });

        // ── TABLE product_images ─────────────────────────────────────
        // Crée la table si elle n'existe pas encore
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->text('image_path');
                $table->boolean('is_primary')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            // Si elle existe, vérifie les colonnes importantes
            Schema::table('product_images', function (Blueprint $table) {
                if (!Schema::hasColumn('product_images', 'is_primary')) {
                    $table->boolean('is_primary')->default(false);
                }
                if (!Schema::hasColumn('product_images', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        // Rollback : supprime les colonnes ajoutées
        $newCols = [
            'name_de', 'description_de', 'is_new', 'assembly_required',
            'brand', 'model_name', 'year', 'bike_type', 'age_range',
            'color', 'colors', 'sizes', 'wheel_size', 'wheel_width',
            'wheel_material', 'frame_material', 'number_of_speeds',
            'suspension_type', 'brake_style', 'power_source', 'motor_power',
            'battery_capacity', 'battery_energy_content', 'max_speed',
            'max_range', 'charging_time', 'weight', 'max_load', 'dimensions',
            'certification', 'waterproof_rating', 'warranty_type',
            'warranty_description', 'included_components', 'special_features',
            'special_features_de',
        ];

        Schema::table('products', function (Blueprint $table) use ($newCols) {
            foreach ($newCols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
