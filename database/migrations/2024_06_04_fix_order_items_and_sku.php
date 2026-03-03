<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Corrige 3 problèmes :
 * 1. product_sku nullable dans order_items
 * 2. product_variant_id nullable dans order_items
 * 3. Ajoute la colonne sku aux products si manquante
 *
 * php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Rendre product_sku et product_variant_id nullables ──────
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'product_sku')) {
                    $table->string('product_sku')->nullable()->change();
                }
                if (Schema::hasColumn('order_items', 'product_variant_id')) {
                    $table->unsignedBigInteger('product_variant_id')->nullable()->change();
                }
                if (Schema::hasColumn('order_items', 'configuration')) {
                    $table->text('configuration')->nullable()->change();
                }
            });
        }

        // ── 2. Ajouter sku aux products ────────────────────────────────
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->string('sku')->nullable()->after('slug');
                }
            });

            // Générer un SKU automatique pour les produits existants
            DB::table('products')
                ->whereNull('sku')
                ->orderBy('id')
                ->each(function ($product) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['sku' => 'VLO-' . str_pad($product->id, 5, '0', STR_PAD_LEFT)]);
                });
        }
    }

    public function down(): void
    {
        // Ne pas annuler le nullable — trop risqué
    }
};
