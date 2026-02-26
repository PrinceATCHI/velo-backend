<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'VTT',
                'slug' => 'vtt',
                'description' => 'Vélos Tout-Terrain pour les aventuriers',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Vélos de Route',
                'slug' => 'velos-de-route',
                'description' => 'Vélos de route pour la vitesse et la performance',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Vélos de Ville',
                'slug' => 'velos-de-ville',
                'description' => 'Vélos urbains pour vos déplacements quotidiens',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Vélos Électriques',
                'slug' => 'velos-electriques',
                'description' => 'Vélos à assistance électrique',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Vélos Enfants',
                'slug' => 'velos-enfants',
                'description' => 'Vélos adaptés aux enfants',
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}