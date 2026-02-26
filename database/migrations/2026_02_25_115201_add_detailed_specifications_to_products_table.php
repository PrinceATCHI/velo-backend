<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Caractéristiques générales
            $table->string('bike_type', 50)->nullable()->after('category_id');
            $table->string('age_range', 50)->nullable()->after('bike_type');
            $table->integer('number_of_speeds')->nullable()->after('age_range');
            $table->string('wheel_size', 20)->nullable()->after('number_of_speeds');
            $table->string('frame_material', 50)->nullable()->after('wheel_size');
            $table->string('suspension_type', 50)->nullable()->after('frame_material');
            $table->text('special_features')->nullable()->after('suspension_type');
            $table->string('brake_style', 50)->nullable()->after('special_features');
            $table->string('wheel_width', 20)->nullable()->after('brake_style');
            
            // Informations produit
            $table->string('model_name', 100)->nullable()->after('wheel_width');
            $table->string('power_source', 50)->nullable()->after('model_name');
            $table->string('wheel_material', 50)->nullable()->after('power_source');
            $table->integer('year')->nullable()->after('wheel_material');
            
            // Batterie et électrique
            $table->string('battery_energy_content', 50)->nullable()->after('year');
            $table->string('battery_capacity', 50)->nullable()->after('battery_energy_content');
            $table->string('max_speed', 20)->nullable()->after('battery_capacity');
            $table->string('max_range', 20)->nullable()->after('max_speed');
            $table->string('motor_power', 50)->nullable()->after('max_range');
            $table->string('charging_time', 50)->nullable()->after('motor_power');
            
            // Poids et dimensions
            $table->string('weight', 20)->nullable()->after('charging_time');
            $table->string('max_load', 20)->nullable()->after('weight');
            $table->string('dimensions', 100)->nullable()->after('max_load');
            $table->string('package_weight', 20)->nullable()->after('dimensions');
            
            // Certifications et garanties
            $table->string('waterproof_rating', 20)->nullable()->after('package_weight');
            $table->string('certification', 100)->nullable()->after('waterproof_rating');
            $table->boolean('assembly_required')->default(true)->after('certification');
            $table->string('warranty_type', 50)->nullable()->after('assembly_required');
            $table->text('warranty_description')->nullable()->after('warranty_type');
            $table->text('included_components')->nullable()->after('warranty_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'bike_type', 'age_range', 'number_of_speeds', 'wheel_size',
                'frame_material', 'suspension_type', 'special_features', 'brake_style',
                'wheel_width', 'model_name', 'power_source', 'wheel_material', 'year',
                'battery_energy_content', 'battery_capacity', 'max_speed', 'max_range',
                'motor_power', 'charging_time', 'weight', 'max_load', 'waterproof_rating',
                'certification', 'assembly_required', 'warranty_type', 'warranty_description',
                'included_components', 'dimensions', 'package_weight'
            ]);
        });
    }
};