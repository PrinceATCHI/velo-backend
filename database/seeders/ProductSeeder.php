<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vtt = Category::where('slug', 'vtt')->first();
        $route = Category::where('slug', 'velos-de-route')->first();
        $ville = Category::where('slug', 'velos-de-ville')->first();
        $electrique = Category::where('slug', 'velos-electriques')->first();

        $products = [
            [
                'category_id' => $vtt->id,
                'name' => 'VTT ProTrail X1',
                'slug' => 'vtt-protrail-x1',
                'description' => 'VTT semi-rigide pour trails techniques',
                'technical_specs' => 'Cadre aluminium, Fourche 120mm, 29 pouces',
                'price' => 1299.99,
                'stock' => 15,
                'sku' => 'VTT-PTX1-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $route->id,
                'name' => 'Route Speedster R8',
                'slug' => 'route-speedster-r8',
                'description' => 'Vélo de route en carbone pour la performance',
                'technical_specs' => 'Cadre carbone, Shimano 105, 28 pouces',
                'price' => 2499.99,
                'sale_price' => 2199.99,
                'stock' => 8,
                'sku' => 'ROUTE-SR8-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $ville->id,
                'name' => 'City Cruiser Classic',
                'slug' => 'city-cruiser-classic',
                'description' => 'Vélo de ville confortable et élégant',
                'technical_specs' => 'Cadre acier, 7 vitesses, panier inclus',
                'price' => 549.99,
                'stock' => 25,
                'sku' => 'CITY-CC-001',
                'is_active' => true,
            ],
            [
                'category_id' => $electrique->id,
                'name' => 'E-Bike PowerMax 500',
                'slug' => 'e-bike-powermax-500',
                'description' => 'Vélo électrique 500W autonomie 80km',
                'technical_specs' => 'Moteur 500W, Batterie 48V 13Ah, Shimano Altus',
                'price' => 1899.99,
                'stock' => 10,
                'sku' => 'EBIKE-PM500-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $vtt->id,
                'name' => 'VTT MountainKing Pro',
                'slug' => 'vtt-mountainking-pro',
                'description' => 'VTT full suspension pour descente',
                'technical_specs' => 'Cadre alu, Fourche 150mm, 27.5 pouces',
                'price' => 1799.99,
                'stock' => 12,
                'sku' => 'VTT-MKP-001',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}