<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BikeComponent;

class BikeComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            // Cadres
            ['type' => 'frame', 'name' => 'Cadre Aluminium Sport', 'description' => 'Cadre léger en aluminium', 'price' => 299.99, 'specifications' => 'Taille M, Aluminium 6061', 'is_available' => true],
            ['type' => 'frame', 'name' => 'Cadre Carbone Pro', 'description' => 'Cadre ultra-léger en carbone', 'price' => 899.99, 'specifications' => 'Taille M, Carbone T700', 'is_available' => true],
            ['type' => 'frame', 'name' => 'Cadre Acier Classic', 'description' => 'Cadre robuste en acier', 'price' => 199.99, 'specifications' => 'Taille M, Acier chromé', 'is_available' => true],

            // Transmissions
            ['type' => 'transmission', 'name' => 'Shimano Deore 10v', 'description' => 'Transmission fiable 10 vitesses', 'price' => 249.99, 'specifications' => '10 vitesses, Shimano Deore', 'is_available' => true],
            ['type' => 'transmission', 'name' => 'Shimano XT 11v', 'description' => 'Transmission haut de gamme', 'price' => 449.99, 'specifications' => '11 vitesses, Shimano XT', 'is_available' => true],
            ['type' => 'transmission', 'name' => 'SRAM NX 12v', 'description' => 'Transmission moderne 12 vitesses', 'price' => 399.99, 'specifications' => '12 vitesses, SRAM NX', 'is_available' => true],

            // Freins
            ['type' => 'brakes', 'name' => 'Freins à disque mécaniques', 'description' => 'Freinage efficace par câble', 'price' => 89.99, 'specifications' => 'Disques 160mm', 'is_available' => true],
            ['type' => 'brakes', 'name' => 'Freins hydrauliques Shimano', 'description' => 'Freinage puissant et progressif', 'price' => 199.99, 'specifications' => 'Disques 180mm, Shimano MT200', 'is_available' => true],
            ['type' => 'brakes', 'name' => 'Freins hydrauliques XT', 'description' => 'Freinage haut de gamme', 'price' => 349.99, 'specifications' => 'Disques 180mm, Shimano XT', 'is_available' => true],

            // Roues
            ['type' => 'wheels', 'name' => 'Roues 27.5" Aluminium', 'description' => 'Roues polyvalentes', 'price' => 149.99, 'specifications' => '27.5", Jantes alu', 'is_available' => true],
            ['type' => 'wheels', 'name' => 'Roues 29" Carbone', 'description' => 'Roues légères et rigides', 'price' => 599.99, 'specifications' => '29", Jantes carbone', 'is_available' => true],
            ['type' => 'wheels', 'name' => 'Roues 26" VTT', 'description' => 'Roues classiques VTT', 'price' => 99.99, 'specifications' => '26", Jantes alu', 'is_available' => true],

            // Selles
            ['type' => 'saddle', 'name' => 'Selle Sport Confort', 'description' => 'Selle rembourrée confortable', 'price' => 39.99, 'specifications' => 'Gel, Rails acier', 'is_available' => true],
            ['type' => 'saddle', 'name' => 'Selle Racing Pro', 'description' => 'Selle performance légère', 'price' => 89.99, 'specifications' => 'Carbone, Rails titane', 'is_available' => true],
            ['type' => 'saddle', 'name' => 'Selle Touring', 'description' => 'Selle longue distance', 'price' => 59.99, 'specifications' => 'Gel extra, Rails alu', 'is_available' => true],

            // Guidons
            ['type' => 'handlebar', 'name' => 'Guidon Plat 680mm', 'description' => 'Guidon classique plat', 'price' => 29.99, 'specifications' => '680mm, Aluminium', 'is_available' => true],
            ['type' => 'handlebar', 'name' => 'Guidon Relevé 720mm', 'description' => 'Guidon large et confortable', 'price' => 49.99, 'specifications' => '720mm, Aluminium', 'is_available' => true],
            ['type' => 'handlebar', 'name' => 'Guidon Carbone Aéro', 'description' => 'Guidon aérodynamique léger', 'price' => 149.99, 'specifications' => '700mm, Carbone', 'is_available' => true],

            // Accessoires
            ['type' => 'accessories', 'name' => 'Éclairage LED avant', 'description' => 'Phare puissant rechargeable', 'price' => 34.99, 'specifications' => '1000 lumens, USB', 'is_available' => true],
            ['type' => 'accessories', 'name' => 'Éclairage LED arrière', 'description' => 'Feu arrière clignotant', 'price' => 19.99, 'specifications' => 'LED rouge, USB', 'is_available' => true],
            ['type' => 'accessories', 'name' => 'Porte-bidon', 'description' => 'Support bouteille aluminium', 'price' => 12.99, 'specifications' => 'Aluminium léger', 'is_available' => true],
            ['type' => 'accessories', 'name' => 'Garde-boue avant/arrière', 'description' => 'Protection contre projections', 'price' => 24.99, 'specifications' => 'Plastique résistant', 'is_available' => true],
            ['type' => 'accessories', 'name' => 'Béquille latérale', 'description' => 'Béquille ajustable', 'price' => 14.99, 'specifications' => 'Aluminium, réglable', 'is_available' => true],
        ];

        foreach ($components as $component) {
            BikeComponent::create($component);
        }
    }
}