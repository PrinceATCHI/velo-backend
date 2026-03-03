<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

/**
 * LANCEMENT :
 *   php artisan db:seed --class=ProductSeeder
 *
 * RESET COMPLET :
 *   php artisan migrate:fresh && php artisan db:seed --class=ProductSeeder
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductImage::truncate();
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /* ══════════════════════════════════════
           CATÉGORIES
        ══════════════════════════════════════ */
        $catData = [
            ['name' => 'VTT',           'name_de' => 'Mountainbike',    'slug' => 'vtt',
             'description' => 'Vélos tout-terrain pour trails et sentiers',
             'description_de' => 'Geländefahrräder für Trails und Wege'],

            ['name' => 'Route',         'name_de' => 'Rennrad',          'slug' => 'route',
             'description' => 'Vélos de route légères et performants',
             'description_de' => 'Leichte und leistungsstarke Rennräder'],

            ['name' => 'Gravel',        'name_de' => 'Gravel',           'slug' => 'gravel',
             'description' => 'Vélos polyvalents route et chemins',
             'description_de' => 'Vielseitige Räder für Straße und Wege'],

            ['name' => 'Ville',         'name_de' => 'Stadtrad',         'slug' => 'ville',
             'description' => 'Vélos urbains et de déplacement quotidien',
             'description_de' => 'Stadträder für den täglichen Einsatz'],

            ['name' => 'Électrique',    'name_de' => 'E-Bike',           'slug' => 'electrique',
             'description' => 'Vélos à assistance électrique toutes catégories',
             'description_de' => 'Elektrofahrräder aller Kategorien'],

            ['name' => 'BMX',           'name_de' => 'BMX',              'slug' => 'bmx',
             'description' => 'BMX freestyle et race pour tous niveaux',
             'description_de' => 'BMX Freestyle und Race für alle Niveaus'],

            ['name' => 'Enfant',        'name_de' => 'Kinderrad',        'slug' => 'enfant',
             'description' => 'Vélos et draisiennes pour les enfants',
             'description_de' => 'Fahrräder und Laufräder für Kinder'],

            ['name' => 'Reconditionné', 'name_de' => 'Generalüberholt',  'slug' => 'reconditionne',
             'description' => 'Vélos reconditionnés certifiés par nos techniciens',
             'description_de' => 'Von unseren Technikern zertifizierte Generalüberholte Räder'],
        ];

        foreach ($catData as $c) {
            Category::create($c);
        }

        $catIds = Category::pluck('id', 'slug');

        /* ══════════════════════════════════════
           HELPER — créer produit + images
        ══════════════════════════════════════ */
        $make = function (array $d) use ($catIds) {
            $images = $d['images'] ?? [];
            $cat    = $d['cat'];
            unset($d['images'], $d['cat']);

            $product = Product::create(array_merge([
                'category_id' => $catIds[$cat] ?? null,
            ], $d));

            foreach ($images as $i => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $url,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        };

        /* ══════════════════════════════════════
           ██ VTT
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'vtt',
            'name'             => 'Trek Marlin 7 Gen 3 29" Noir',
            'name_de'          => 'Trek Marlin 7 Gen 3 29" Schwarz',
            'slug'             => 'trek-marlin-7-gen3-29-noir',
            'description'      => "Le Trek Marlin 7 est le VTT hardtail parfait pour débuter sur les sentiers. Sa fourche RockShox Judy 100 mm absorbe les chocs avec efficacité, tandis que la transmission Shimano Deore 2×10 vitesses couvre tous les dénivelés. Le cadre aluminium Alpha Platinum offre légèreté et solidité pour progresser en toute confiance sur les terrains variés.",
            'description_de'   => "Das Trek Marlin 7 ist das perfekte Hardtail-MTB für den Einstieg in die Trails. Die RockShox Judy Gabel mit 100 mm Federweg absorbiert Stöße effizient, während der Shimano Deore 2×10-Gang-Antrieb alle Anstiege bewältigt. Der leichte und robuste Alpha Platinum Aluminiumrahmen gibt Ihnen das Vertrauen, auf verschiedenen Geländearten voranzukommen.",
            'price'            => 1299.00,
            'sale_price'       => 999.00,
            'stock'            => 12,
            'is_featured'      => true,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'Hardtail VTT',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium Alpha Platinum',
            'number_of_speeds' => '20 vitesses',
            'suspension_type'  => 'RockShox Judy 100 mm',
            'brake_style'      => 'Hydraulique Shimano MT200',
            'weight'           => '14,2 kg',
            'warranty_type'    => 'Garantie Trek à vie cadre',
            'colors'           => 'Noir,Bleu,Rouge',
            'sizes'            => 'XS,S,M,L,XL,XXL',
            'images'           => [
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
                'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=900&q=85',
                'https://images.unsplash.com/photo-1576435728678-68d0fbf94946?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'vtt',
            'name'             => 'Specialized Stumpjumper Comp Alloy 29"',
            'name_de'          => 'Specialized Stumpjumper Comp Alloy 29"',
            'slug'             => 'specialized-stumpjumper-comp-alloy-29',
            'description'      => "Le Stumpjumper Comp Alloy est le trail bike par excellence. Avec 140 mm de débattement avant (Fox 36 Rhythm) et arrière (Fox Float DPS), il avale les obstacles du trail avec une aisance remarquable. La transmission SRAM SX Eagle 12 vitesses et les freins SRAM G2 R complètent un package taillé pour la performance.",
            'description_de'   => "Das Stumpjumper Comp Alloy ist das Trail-Bike schlechthin. Mit 140 mm Federweg vorne (Fox 36 Rhythm) und hinten (Fox Float DPS) schluckt es Hindernisse mit bemerkenswerter Leichtigkeit. Die SRAM SX Eagle 12-Gang Schaltung und SRAM G2 R Bremsen vervollständigen ein auf Leistung ausgerichtetes Paket.",
            'price'            => 3299.00,
            'sale_price'       => null,
            'stock'            => 6,
            'is_featured'      => true,
            'is_new'           => true,
            'brand'            => 'Specialized',
            'bike_type'        => 'Tout-suspendu Trail',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium M5',
            'number_of_speeds' => '12 vitesses SRAM SX Eagle',
            'suspension_type'  => 'Fox 36 Rhythm 140 mm / Fox Float DPS 140 mm',
            'brake_style'      => 'Hydraulique SRAM G2 R',
            'weight'           => '13,8 kg',
            'warranty_type'    => 'Garantie Specialized 5 ans cadre',
            'colors'           => 'Bleu,Vert,Noir',
            'sizes'            => 'S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1571333250630-f0230c320b6d?w=900&q=85',
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'vtt',
            'name'             => 'Scott Genius 940 29" Tout-Suspendu',
            'name_de'          => 'Scott Genius 940 29" Fully',
            'slug'             => 'scott-genius-940-29-tout-suspendu',
            'description'      => "Le Scott Genius 940 combine légèreté et polyvalence pour les vttistes exigeants. Son système exclusif TwinLoc permet de basculer instantanément entre les modes Trail, Traction et Lock via une seule commande au guidon, adaptant la géométrie aux besoins du terrain.",
            'description_de'   => "Das Scott Genius 940 kombiniert Leichtigkeit und Vielseitigkeit für anspruchsvolle Mountainbiker. Das exklusive TwinLoc-System ermöglicht es, sofort zwischen Trail-, Traktions- und Sperrmodi über einen einzigen Lenkerschalter zu wechseln.",
            'price'            => 2499.00,
            'sale_price'       => 2099.00,
            'stock'            => 4,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Scott',
            'bike_type'        => 'Tout-suspendu Enduro',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium 6061',
            'number_of_speeds' => '12 vitesses Shimano Deore',
            'suspension_type'  => 'RockShox Recon 130 mm / Scott TwinLoc 130 mm',
            'brake_style'      => 'Hydraulique Shimano MT420',
            'weight'           => '14,5 kg',
            'warranty_type'    => 'Garantie Scott 2 ans',
            'colors'           => 'Bleu acier,Noir',
            'sizes'            => 'S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1553764854-53ac4f1e175b?w=900&q=85',
                'https://images.unsplash.com/photo-1576435728678-68d0fbf94946?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'vtt',
            'name'             => 'Cube Aim Pro 27.5" Hardtail',
            'name_de'          => 'Cube Aim Pro 27.5" Hardtail',
            'slug'             => 'cube-aim-pro-27-5-hardtail',
            'description'      => "Le Cube Aim Pro est le VTT hardtail entrée de gamme sans compromis. Sa fourche SR Suntour XCR 100 mm et sa transmission Shimano Acera 3×8 vitesses en font un compagnon fiable pour découvrir les chemins forestiers et les sentiers de montagne.",
            'description_de'   => "Das Cube Aim Pro ist das Hardtail-Einsteiger-MTB ohne Kompromisse. Die SR Suntour XCR 100 mm Gabel und der Shimano Acera 3×8-Gang-Antrieb machen es zu einem zuverlässigen Begleiter für Waldwege und Bergpfade.",
            'price'            => 799.00,
            'sale_price'       => null,
            'stock'            => 16,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Cube',
            'bike_type'        => 'Hardtail VTT',
            'wheel_size'       => '27.5"',
            'frame_material'   => 'Aluminium 6061',
            'number_of_speeds' => '24 vitesses Shimano Acera',
            'suspension_type'  => 'SR Suntour XCR 100 mm',
            'brake_style'      => 'Disque hydraulique Shimano',
            'weight'           => '13,6 kg',
            'warranty_type'    => 'Garantie Cube 2 ans',
            'colors'           => 'Vert/Noir,Gris/Noir,Bleu/Noir',
            'sizes'            => 'XS,S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
                'https://images.unsplash.com/photo-1553764854-53ac4f1e175b?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'vtt',
            'name'             => 'Cannondale Habit 4 29" Trail Bike',
            'name_de'          => 'Cannondale Habit 4 29" Trail Bike',
            'slug'             => 'cannondale-habit-4-29-trail',
            'description'      => "Le Cannondale Habit 4 est un trail bike polyvalent capable de tout faire. Sa géométrie Progressive Sizing offre une conduite naturelle, et son amortisseur Cannondale Proportional Response garantit un grip optimal aussi bien en montée qu'en descente technique.",
            'description_de'   => "Das Cannondale Habit 4 ist ein vielseitiges Trail-Bike, das alles kann. Seine Progressive-Sizing-Geometrie bietet ein natürliches Fahrgefühl, und der Cannondale Proportional Response Dämpfer garantiert optimalen Grip bergauf wie bergab.",
            'price'            => 2799.00,
            'sale_price'       => null,
            'stock'            => 8,
            'is_featured'      => false,
            'is_new'           => true,
            'brand'            => 'Cannondale',
            'bike_type'        => 'Tout-suspendu Trail',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium SmartForm C2',
            'number_of_speeds' => '12 vitesses SRAM SX Eagle',
            'suspension_type'  => 'RockShox Recon 130 mm / Fox Float',
            'brake_style'      => 'Hydraulique Shimano MT420',
            'weight'           => '14,1 kg',
            'warranty_type'    => 'Garantie Cannondale à vie cadre',
            'colors'           => 'Vert forêt,Noir',
            'sizes'            => 'S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1559348349-86f1f65817fe?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ ROUTE
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'route',
            'name'             => 'Trek Émonda SL 5 Disc Carbone',
            'name_de'          => 'Trek Émonda SL 5 Disc Carbon',
            'slug'             => 'trek-emonda-sl5-disc-carbone',
            'description'      => "Conçu pour la légèreté absolue et la rigidité maximale, l'Émonda SL 5 Disc embarque un cadre carbone OCLV 500 parmi les plus légers du marché. Sa transmission Shimano 105 Di2 électronique et ses freins à disque hydrauliques en font un vélo de course redoutable sur toutes les routes.",
            'description_de'   => "Für absolute Leichtigkeit und maximale Steifigkeit konzipiert, verfügt das Émonda SL 5 Disc über einen OCLV 500 Carbon-Rahmen, der zu den leichtesten auf dem Markt gehört. Die elektronische Shimano 105 Di2 Schaltung und hydraulische Scheibenbremsen machen es zu einem gefährlichen Rennrad auf allen Straßen.",
            'price'            => 4299.00,
            'sale_price'       => 3499.00,
            'stock'            => 5,
            'is_featured'      => true,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'Vélo de route',
            'wheel_size'       => '700c',
            'frame_material'   => 'Carbone OCLV 500',
            'number_of_speeds' => '22 vitesses Shimano 105 Di2',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Disque hydraulique Shimano 105',
            'weight'           => '7,9 kg',
            'warranty_type'    => 'Garantie Trek à vie cadre carbone',
            'colors'           => 'Blanc,Noir,Rouge',
            'sizes'            => '47,50,52,54,56,58,60,62',
            'images'           => [
                'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=900&q=85',
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'route',
            'name'             => 'Specialized Tarmac SL7 Expert Ultegra Di2',
            'name_de'          => 'Specialized Tarmac SL7 Expert Ultegra Di2',
            'slug'             => 'specialized-tarmac-sl7-expert-ultegra-di2',
            'description'      => "Le Tarmac SL7 Expert est la quintessence du vélo de route carbone accessible. Son groupe Shimano Ultegra Di2 et ses roues Roval Rapide CLX offrent des performances dignes d'un vélo de compétition. Le cadre FACT 10r et son aérodynamisme de pointe vous permettent de rouler plus vite avec moins d'efforts.",
            'description_de'   => "Das Tarmac SL7 Expert ist die Quintessenz des zugänglichen Carbon-Rennrads. Die Shimano Ultegra Di2 Gruppe und Roval Rapide CLX Laufräder bieten Wettkampfleistung. Der FACT 10r Rahmen und sein überlegener Aerodynamismus ermöglichen schnelleres Fahren mit weniger Aufwand.",
            'price'            => 5499.00,
            'sale_price'       => null,
            'stock'            => 3,
            'is_featured'      => true,
            'is_new'           => true,
            'brand'            => 'Specialized',
            'bike_type'        => 'Vélo de route aero',
            'wheel_size'       => '700c',
            'frame_material'   => 'Carbone FACT 10r',
            'number_of_speeds' => '22 vitesses Shimano Ultegra Di2',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Disque hydraulique Shimano Ultegra',
            'weight'           => '7,1 kg',
            'warranty_type'    => 'Garantie Specialized 5 ans cadre',
            'colors'           => 'Noir mat,Blanc,Bleu',
            'sizes'            => '44,49,52,54,56,58,61',
            'images'           => [
                'https://images.unsplash.com/photo-1526307616774-60d0098f7642?w=900&q=85',
                'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'route',
            'name'             => 'Cannondale CAAD13 105 Disc Aluminium',
            'name_de'          => 'Cannondale CAAD13 105 Disc Aluminium',
            'slug'             => 'cannondale-caad13-105-disc',
            'description'      => "Le CAAD13 est unanimement reconnu comme le meilleur vélo de route en aluminium. Son cadre SmartForm C1 Premium Alloy offre une rigidité et une légèreté qui rivalise avec le carbone. Avec le groupe Shimano 105 et des freins à disque, il est redoutable sur la route.",
            'description_de'   => "Das CAAD13 ist allgemein als das beste Aluminium-Rennrad anerkannt. Sein SmartForm C1 Premium Alloy Rahmen bietet eine Steifigkeit und Leichtigkeit, die mit Carbon konkurriert. Mit der Shimano 105 Gruppe und Scheibenbremsen ist es auf der Straße gefährlich.",
            'price'            => 1899.00,
            'sale_price'       => null,
            'stock'            => 9,
            'is_featured'      => false,
            'is_new'           => true,
            'brand'            => 'Cannondale',
            'bike_type'        => 'Vélo de route',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium SmartForm C1 Premium',
            'number_of_speeds' => '22 vitesses Shimano 105',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Disque hydraulique Shimano 105',
            'weight'           => '8,6 kg',
            'warranty_type'    => 'Garantie Cannondale à vie cadre',
            'colors'           => 'Blanc/Bleu,Noir,Gris',
            'sizes'            => '44,48,51,54,56,58,61',
            'images'           => [
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=85',
                'https://images.unsplash.com/photo-1526307616774-60d0098f7642?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ GRAVEL
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'gravel',
            'name'             => 'Trek Checkpoint ALR 5 Gravel',
            'name_de'          => 'Trek Checkpoint ALR 5 Gravel',
            'slug'             => 'trek-checkpoint-alr5-gravel',
            'description'      => "Le Checkpoint ALR 5 est le gravel bike ultime pour les aventuriers. Son cadre aluminium IsoSpeed découplé absorbe les vibrations sur les pistes caillouteuses tout en restant vif sur route. Monté en pneus 700×40c, il est prêt pour les longues aventures mixtes.",
            'description_de'   => "Das Checkpoint ALR 5 ist das ultimative Gravel-Bike für Abenteurer. Der entkoppelte IsoSpeed-Aluminiumrahmen absorbiert Vibrationen auf Schotterpisten und bleibt dennoch lebhaft auf der Straße. Mit 700×40c Reifen ist es bereit für lange gemischte Abenteuer.",
            'price'            => 1999.00,
            'sale_price'       => null,
            'stock'            => 9,
            'is_featured'      => false,
            'is_new'           => true,
            'brand'            => 'Trek',
            'bike_type'        => 'Gravel / Aventure',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium Alpha Platinum IsoSpeed',
            'number_of_speeds' => '20 vitesses Shimano GRX',
            'suspension_type'  => 'Rigide IsoSpeed',
            'brake_style'      => 'Disque hydraulique Shimano GRX',
            'weight'           => '9,4 kg',
            'warranty_type'    => 'Garantie Trek à vie cadre',
            'colors'           => 'Gris ardoise,Noir,Vert',
            'sizes'            => '47,50,52,54,56,58,61',
            'images'           => [
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
                'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'gravel',
            'name'             => 'Specialized Diverge Comp Carbon Gravel',
            'name_de'          => 'Specialized Diverge Comp Carbon Gravel',
            'slug'             => 'specialized-diverge-comp-carbon-gravel',
            'description'      => "Le Diverge Comp Carbon redéfinit le confort en gravel. Son système Future Shock 2.0 à l'avant absorbe les vibrations pour un confort maximal sur les longues distances. Le cadre carbone FACT 9r et la transmission Shimano GRX Di2 électronique en font un gravel bike de référence.",
            'description_de'   => "Das Diverge Comp Carbon definiert den Gravel-Komfort neu. Das Future Shock 2.0 System vorne absorbiert Vibrationen für maximalen Komfort auf langen Strecken. Der FACT 9r Carbon-Rahmen und die elektronische Shimano GRX Di2 Schaltung machen es zu einem Referenz-Gravel-Bike.",
            'price'            => 3799.00,
            'sale_price'       => 3199.00,
            'stock'            => 4,
            'is_featured'      => true,
            'is_new'           => false,
            'brand'            => 'Specialized',
            'bike_type'        => 'Gravel Endurance',
            'wheel_size'       => '700c',
            'frame_material'   => 'Carbone FACT 9r',
            'number_of_speeds' => '22 vitesses Shimano GRX Di2',
            'suspension_type'  => 'Future Shock 2.0 (avant)',
            'brake_style'      => 'Disque hydraulique Shimano GRX',
            'weight'           => '8,7 kg',
            'warranty_type'    => 'Garantie Specialized 5 ans cadre',
            'colors'           => 'Sable,Bleu,Noir',
            'sizes'            => '44,49,52,54,56,58,61',
            'images'           => [
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=85',
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ VILLE
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'ville',
            'name'             => 'Trek FX 3 Disc Fitness & Ville',
            'name_de'          => 'Trek FX 3 Disc Fitness & Stadt',
            'slug'             => 'trek-fx3-disc-fitness-ville',
            'description'      => "Le Trek FX 3 Disc est le vélo de ville par excellence. Léger, confortable et rapide, il s'adapte à tous les besoins : trajets quotidiens, balades du week-end ou sorties sportives. Sa fourche en carbone absorbe les chocs de la ville et ses freins à disque garantissent un arrêt fiable par tous les temps.",
            'description_de'   => "Das Trek FX 3 Disc ist das Stadtrad schlechthin. Leicht, komfortabel und schnell, passt es sich allen Bedürfnissen an: tägliche Pendelfahrten, Wochenendausflüge oder sportliche Ausfahrten. Die Carbon-Gabel absorbiert Stadterschütterungen und Scheibenbremsen garantieren zuverlässiges Bremsen bei jedem Wetter.",
            'price'            => 899.00,
            'sale_price'       => null,
            'stock'            => 15,
            'is_featured'      => true,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'Fitness / Ville',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium Alpha Gold',
            'number_of_speeds' => '24 vitesses Shimano Acera',
            'suspension_type'  => 'Fourche rigide carbone',
            'brake_style'      => 'Disque hydraulique Shimano',
            'weight'           => '10,8 kg',
            'warranty_type'    => 'Garantie Trek à vie cadre',
            'colors'           => 'Gris argent,Noir,Bleu',
            'sizes'            => 'XS,S,M,L,XL,XXL',
            'images'           => [
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'ville',
            'name'             => 'Cube Hyde Race Commuter Urbain',
            'name_de'          => 'Cube Hyde Race Pendler Stadtrad',
            'slug'             => 'cube-hyde-race-commuter-urbain',
            'description'      => "Le Cube Hyde Race est pensé pour les cyclistes urbains qui ne veulent pas sacrifier la performance. Son cadre aluminium 6061 léger et sa transmission Shimano Deore 10 vitesses lui permettent de distancer les embouteillages avec aisance, tout en restant parfaitement équipé pour la ville.",
            'description_de'   => "Das Cube Hyde Race ist für urbane Radfahrer gedacht, die keine Leistung opfern wollen. Sein leichter Aluminium 6061 Rahmen und die Shimano Deore 10-Gang-Schaltung ermöglichen es, Staus mühelos zu überholen und dabei perfekt für die Stadt ausgestattet zu sein.",
            'price'            => 999.00,
            'sale_price'       => null,
            'stock'            => 10,
            'is_featured'      => false,
            'is_new'           => true,
            'brand'            => 'Cube',
            'bike_type'        => 'Vélo de ville / Commuter',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium 6061',
            'number_of_speeds' => '10 vitesses Shimano Deore',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Disque hydraulique Shimano',
            'weight'           => '10,4 kg',
            'warranty_type'    => 'Garantie Cube 2 ans',
            'colors'           => 'Gris/Vert,Noir/Gris,Bleu/Blanc',
            'sizes'            => 'XS,S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=85',
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ ÉLECTRIQUE
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'electrique',
            'name'             => 'Trek Powerfly FS 9 VTT Électrique Tout-Suspendu',
            'name_de'          => 'Trek Powerfly FS 9 Fully E-MTB',
            'slug'             => 'trek-powerfly-fs9-vtt-electrique',
            'description'      => "Le Trek Powerfly FS 9 est le VTT électrique tout-suspendu de référence. Son moteur Bosch Performance CX de 85 Nm et sa batterie 625 Wh lui confèrent une autonomie exceptionnelle pour conquérir les chemins les plus techniques. La fourche Fox 36 Rhythm 140 mm assure un contrôle parfait en toutes conditions.",
            'description_de'   => "Das Trek Powerfly FS 9 ist das Referenz-Fully E-MTB. Sein Bosch Performance CX Motor mit 85 Nm und der 625 Wh Akku verleihen ihm eine außergewöhnliche Reichweite für die technischsten Trails. Die Fox 36 Rhythm 140 mm Gabel sorgt bei allen Bedingungen für perfekte Kontrolle.",
            'price'            => 6499.00,
            'sale_price'       => 5799.00,
            'stock'            => 5,
            'is_featured'      => true,
            'is_new'           => true,
            'brand'            => 'Trek',
            'bike_type'        => 'VTT électrique tout-suspendu',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium Alpha Platinum',
            'number_of_speeds' => '12 vitesses Shimano Deore XT',
            'suspension_type'  => 'Fox 36 Rhythm 140 mm / Fox Float DPS 140 mm',
            'brake_style'      => 'Hydraulique Shimano Deore XT 4 pistons',
            'power_source'     => 'Batterie lithium-ion intégrée',
            'motor_power'      => 'Bosch Performance CX 250 W / 85 Nm',
            'battery_capacity' => '625 Wh',
            'max_speed'        => '25 km/h (assistance électrique)',
            'max_range'        => '100 km en mode Eco',
            'charging_time'    => '4h30 (charge complète)',
            'weight'           => '24,5 kg',
            'warranty_type'    => 'Garantie Trek 2 ans + Bosch 2 ans batterie',
            'colors'           => 'Anthracite,Noir,Bleu',
            'sizes'            => 'S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1559348349-86f1f65817fe?w=900&q=85',
                'https://images.unsplash.com/photo-1571333250630-f0230c320b6d?w=900&q=85',
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'electrique',
            'name'             => 'Specialized Turbo Levo SL Comp Carbon',
            'name_de'          => 'Specialized Turbo Levo SL Comp Carbon',
            'slug'             => 'specialized-turbo-levo-sl-comp-carbon',
            'description'      => "Le Turbo Levo SL est révolutionnaire : le premier VTT électrique carbone à peser seulement 17,5 kg. Son moteur SL 1.1 de 240 W apporte juste la bonne dose d'assistance pour rouler plus loin sans perdre les sensations du VTT. La batterie 320 Wh est extensible jusqu'à 480 Wh avec le Range Extender.",
            'description_de'   => "Das Turbo Levo SL ist revolutionär: das erste Carbon-E-MTB, das nur 17,5 kg wiegt. Sein SL 1.1 Motor mit 240 W bietet genau die richtige Unterstützung, um weiter zu fahren ohne das MTB-Feeling zu verlieren. Der 320 Wh Akku ist mit dem Range Extender auf 480 Wh erweiterbar.",
            'price'            => 8999.00,
            'sale_price'       => null,
            'stock'            => 3,
            'is_featured'      => true,
            'is_new'           => true,
            'brand'            => 'Specialized',
            'bike_type'        => 'VTT électrique carbone léger',
            'wheel_size'       => '29"',
            'frame_material'   => 'Carbone FACT 11m',
            'number_of_speeds' => '12 vitesses SRAM GX Eagle',
            'suspension_type'  => 'Fox 34 Float 130 mm / Fox Float DPS 130 mm',
            'brake_style'      => 'Hydraulique SRAM G2 R',
            'power_source'     => 'Batterie lithium-ion intégrée',
            'motor_power'      => 'Specialized SL 1.1 240 W / 35 Nm',
            'battery_capacity' => '320 Wh (+ Range Extender 160 Wh)',
            'max_speed'        => '25 km/h (assistance électrique)',
            'max_range'        => '130 km (avec Range Extender)',
            'charging_time'    => '2h30 (charge complète)',
            'weight'           => '17,5 kg',
            'warranty_type'    => 'Garantie Specialized 5 ans cadre',
            'colors'           => 'Vert forêt,Noir,Blanc',
            'sizes'            => 'S,M,L,XL',
            'special_features' => "• Moteur ultral-léger SL 1.1 (-40% de poids vs concurrents)\n• Batterie intégrée invisible dans le cadre\n• Compatible Range Extender pour +160 Wh\n• Application Mission Control pour personnaliser l'assistance",
            'special_features_de' => "• Ultraleichter SL 1.1 Motor (-40% Gewicht vs. Konkurrenz)\n• Unsichtbar in den Rahmen integrierter Akku\n• Range Extender kompatibel für +160 Wh\n• Mission Control App zur Anpassung der Unterstützung",
            'images'           => [
                'https://images.unsplash.com/photo-1559348349-86f1f65817fe?w=900&q=85',
                'https://images.unsplash.com/photo-1571333250630-f0230c320b6d?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'electrique',
            'name'             => 'Giant Explore E+ 2 Pro Trekking Électrique',
            'name_de'          => 'Giant Explore E+ 2 Pro E-Trekking',
            'slug'             => 'giant-explore-e-plus-2-pro-trekking',
            'description'      => "Le Giant Explore E+ 2 Pro est le compagnon idéal pour vos déplacements urbains et vos longues randonnées. Son moteur SyncDrive Pro2 de 80 Nm et sa batterie EnergyPak 500 Wh vous garantissent une autonomie confortable pour explorer sans limites.",
            'description_de'   => "Das Giant Explore E+ 2 Pro ist der ideale Begleiter für Ihre Stadtfahrten und langen Touren. Sein SyncDrive Pro2 Motor mit 80 Nm und der 500 Wh EnergyPak Akku garantieren Ihnen eine komfortable Reichweite für grenzenlose Erkundungen.",
            'price'            => 3299.00,
            'sale_price'       => 2899.00,
            'stock'            => 8,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Giant',
            'bike_type'        => 'Vélo électrique trekking',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium ALUXX',
            'number_of_speeds' => '11 vitesses Shimano Deore',
            'suspension_type'  => 'Fourche télescopique 63 mm',
            'brake_style'      => 'Disque hydraulique Shimano Deore',
            'power_source'     => 'Batterie lithium-ion intégrée',
            'motor_power'      => 'Giant SyncDrive Pro2 250 W / 80 Nm',
            'battery_capacity' => '500 Wh',
            'max_speed'        => '25 km/h (assistance électrique)',
            'max_range'        => '100 km en mode Eco',
            'charging_time'    => '4h (charge complète)',
            'weight'           => '22,3 kg',
            'warranty_type'    => 'Garantie Giant 2 ans + 2 ans batterie',
            'colors'           => 'Noir,Gris,Bleu marine',
            'sizes'            => 'XS,S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1559348349-86f1f65817fe?w=900&q=85',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'electrique',
            'name'             => 'Trek Verve+ 3 Lowstep Vélo Électrique Urbain',
            'name_de'          => 'Trek Verve+ 3 Lowstep E-Bike Stadt',
            'slug'             => 'trek-verve-plus-3-lowstep-urbain',
            'description'      => "Le Trek Verve+ 3 Lowstep est le vélo électrique parfait pour les déplacements quotidiens. Son cadre step-through facilite la montée et la descente, et son équipement complet (garde-boues, porte-bagages, éclairage intégré) le rend prêt à l'emploi dès la sortie du magasin.",
            'description_de'   => "Das Trek Verve+ 3 Lowstep ist das perfekte E-Bike für den täglichen Einsatz. Der Step-Through-Rahmen erleichtert Ein- und Aussteigen, und die Vollausstattung (Schutzbleche, Gepäckträger, integrierte Beleuchtung) macht es ab dem ersten Tag einsatzbereit.",
            'price'            => 2799.00,
            'sale_price'       => 2399.00,
            'stock'            => 7,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'Vélo électrique urbain step-through',
            'wheel_size'       => '700c',
            'frame_material'   => 'Aluminium Alpha',
            'number_of_speeds' => '9 vitesses Shimano Alivio',
            'suspension_type'  => 'Fourche rigide carbone',
            'brake_style'      => 'Disque hydraulique Shimano MT200',
            'power_source'     => 'Batterie lithium-ion amovible',
            'motor_power'      => 'Bosch Active Line Plus 250 W / 50 Nm',
            'battery_capacity' => '500 Wh',
            'max_speed'        => '25 km/h (assistance électrique)',
            'max_range'        => '90 km en mode Eco',
            'charging_time'    => '4h (charge complète)',
            'weight'           => '23,6 kg',
            'included_components' => "Garde-boues avant/arrière\nPorte-bagages arrière\nBéquille latérale\nÉclairage avant/arrière intégré",
            'warranty_type'    => 'Garantie Trek 2 ans + Bosch 2 ans',
            'colors'           => 'Gris perle,Noir,Blanc',
            'sizes'            => 'XS,S,M,L,XL',
            'images'           => [
                'https://images.unsplash.com/photo-1559348349-86f1f65817fe?w=900&q=85',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ BMX
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'bmx',
            'name'             => 'Mongoose Legion L60 20" Freestyle',
            'name_de'          => 'Mongoose Legion L60 20" Freestyle',
            'slug'             => 'mongoose-legion-l60-20-freestyle',
            'description'      => "Le Mongoose Legion L60 est le BMX freestyle idéal pour les riders de niveau intermédiaire. Son cadre en acier Hi-Ten robuste supporte les figures les plus brutales, tandis que ses roues à rayons renforcés et ses pneus larges garantissent stabilité et grip au skatepark comme dans la rue.",
            'description_de'   => "Das Mongoose Legion L60 ist das ideale Freestyle-BMX für Fahrer mittleren Niveaus. Der robuste Hi-Ten Stahlrahmen hält die härtesten Tricks aus, während verstärkte Speichenräder und breite Reifen Stabilität und Grip im Skatepark und auf der Straße garantieren.",
            'price'            => 399.00,
            'sale_price'       => 329.00,
            'stock'            => 14,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Mongoose',
            'bike_type'        => 'BMX Freestyle',
            'wheel_size'       => '20"',
            'frame_material'   => 'Acier Hi-Ten',
            'number_of_speeds' => '1 vitesse (mono-plateau)',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Patins V-Brake arrière uniquement',
            'weight'           => '11,8 kg',
            'warranty_type'    => 'Garantie constructeur 1 an',
            'colors'           => 'Rouge,Noir,Bleu',
            'images'           => [
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'bmx',
            'name'             => 'Wethepeople Crysis 20" Race Pro',
            'name_de'          => 'Wethepeople Crysis 20" Race Pro',
            'slug'             => 'wethepeople-crysis-20-race-pro',
            'description'      => "Le Wethepeople Crysis est le BMX race conçu pour la compétition. Ultra-léger grâce à son cadre aluminium 6061 T6, réactif et précis, il est le choix des riders qui veulent performer sur la piste. Sa géométrie agressive garantit une accélération explosive et une ligne de conduite parfaite.",
            'description_de'   => "Das Wethepeople Crysis ist das für den Wettkampf konzipierte Race-BMX. Ultraleicht dank des Aluminium 6061 T6 Rahmens, reaktionsschnell und präzise, ist es die Wahl für Fahrer, die auf der Strecke performen wollen. Seine aggressive Geometrie garantiert explosive Beschleunigung und perfekte Linie.",
            'price'            => 699.00,
            'sale_price'       => null,
            'stock'            => 6,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Wethepeople',
            'bike_type'        => 'BMX Race',
            'wheel_size'       => '20"',
            'frame_material'   => 'Aluminium 6061 T6',
            'number_of_speeds' => '1 vitesse',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Sans frein (race)',
            'weight'           => '9,2 kg',
            'warranty_type'    => 'Garantie Wethepeople 2 ans',
            'colors'           => 'Noir,Chrome,Blanc',
            'images'           => [
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ ENFANT
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'enfant',
            'name'             => 'Trek Precaliber 20" 7V Vélo Enfant',
            'name_de'          => 'Trek Precaliber 20" 7G Kinderfahrrad',
            'slug'             => 'trek-precaliber-20-7v-enfant',
            'description'      => "Le Trek Precaliber 20\" est le vélo idéal pour les enfants de 6 à 10 ans qui souhaitent progresser rapidement. Sa transmission 7 vitesses Shimano et ses freins à main ajustables grandissent avec l'enfant. Le cadre aluminium léger le rend facile à manier en toutes circonstances.",
            'description_de'   => "Das Trek Precaliber 20\" ist das ideale Fahrrad für Kinder von 6 bis 10 Jahren, die schnell Fortschritte machen möchten. Die 7-Gang Shimano Schaltung und anpassbare Handbremsen wachsen mit dem Kind mit. Der leichte Aluminiumrahmen macht es in jeder Situation leicht handhabbar.",
            'price'            => 499.00,
            'sale_price'       => 449.00,
            'stock'            => 20,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'Vélo enfant',
            'wheel_size'       => '20"',
            'age_range'        => '6 – 10 ans',
            'frame_material'   => 'Aluminium Alpha',
            'number_of_speeds' => '7 vitesses Shimano',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Patins ajustables avant/arrière',
            'weight'           => '9,3 kg',
            'max_load'         => '50 kg',
            'warranty_type'    => 'Garantie Trek à vie cadre',
            'colors'           => 'Rose,Bleu,Vert,Noir',
            'images'           => [
                'https://images.unsplash.com/photo-1503455637927-730bce8583c0?w=900&q=85',
                'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'enfant',
            'name'             => 'Scamp TallFox VTT Semi-Rigide Enfant 24"',
            'name_de'          => 'Scamp TallFox MTB Kinder Hardtail 24"',
            'slug'             => 'scamp-tallfox-vtt-enfant-24',
            'description'      => "Le Scamp TallFox 24\" est un vrai VTT enfant pour les jeunes riders de 8 à 13 ans. Sa fourche suspendue et ses composants Shimano de qualité en font un vélo capable d'accompagner les enfants sur tous les terrains, en développant leur goût pour le vélo.",
            'description_de'   => "Das Scamp TallFox 24\" ist ein echtes Kinder-MTB für junge Fahrer von 8 bis 13 Jahren. Die Federgabel und hochwertige Shimano-Komponenten machen es zu einem Fahrrad, das Kinder auf allen Geländearten begleiten kann.",
            'price'            => 599.00,
            'sale_price'       => null,
            'stock'            => 12,
            'is_featured'      => false,
            'is_new'           => true,
            'brand'            => 'Scamp',
            'bike_type'        => 'VTT enfant hardtail',
            'wheel_size'       => '24"',
            'age_range'        => '8 – 13 ans',
            'frame_material'   => 'Aluminium 6061',
            'number_of_speeds' => '7 vitesses Shimano Tourney',
            'suspension_type'  => 'Fourche suspendue 50 mm',
            'brake_style'      => 'Disque mécanique avant / V-Brake arrière',
            'weight'           => '10,5 kg',
            'max_load'         => '65 kg',
            'warranty_type'    => 'Garantie constructeur 2 ans',
            'colors'           => 'Blanc,Bleu,Rouge',
            'images'           => [
                'https://images.unsplash.com/photo-1503455637927-730bce8583c0?w=900&q=85',
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'enfant',
            'name'             => 'Puky LR Ride 12" Draisienne Enfant',
            'name_de'          => 'Puky LR Ride 12" Laufrad Kind',
            'slug'             => 'puky-lr-ride-12-draisienne',
            'description'      => "La draisienne Puky LR Ride est la référence pour initier les tout-petits de 2 à 5 ans au sens de l'équilibre. Sa conception ergonomique, son guidon et sa selle réglables en hauteur et ses matériaux de qualité en font un choix sûr et durable.",
            'description_de'   => "Das Puky LR Ride Laufrad ist die Referenz, um Kleinkinder von 2 bis 5 Jahren an das Gleichgewicht heranzuführen. Das ergonomische Design, höhenverstellbarer Lenker und Sattel sowie hochwertige Materialien machen es zu einer sicheren und langlebigen Wahl.",
            'price'            => 149.00,
            'sale_price'       => 129.00,
            'stock'            => 25,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Puky',
            'bike_type'        => 'Draisienne / Balance Bike',
            'wheel_size'       => '12"',
            'age_range'        => '2 – 5 ans',
            'frame_material'   => 'Acier',
            'number_of_speeds' => 'Sans vitesses',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Frein à main arrière',
            'weight'           => '3,5 kg',
            'max_load'         => '25 kg',
            'warranty_type'    => 'Garantie Puky 2 ans',
            'colors'           => 'Bleu,Rose,Rouge,Vert',
            'assembly_required'=> true,
            'included_components' => "Clé de montage\nNotice d'assemblage multilingue",
            'images'           => [
                'https://images.unsplash.com/photo-1503455637927-730bce8583c0?w=900&q=85',
            ],
        ]);

        /* ══════════════════════════════════════
           ██ RECONDITIONNÉ
        ══════════════════════════════════════ */
        $make([
            'cat'              => 'reconditionne',
            'name'             => 'Trek Marlin 5 29" Reconditionné Grade A',
            'name_de'          => 'Trek Marlin 5 29" Generalüberholt Klasse A',
            'slug'             => 'trek-marlin-5-29-reconditionne-grade-a',
            'description'      => "Ce Trek Marlin 5 reconditionné Grade A a été intégralement révisé par nos techniciens certifiés. La transmission, les freins et les roulements ont été vérifiés et remplacés si nécessaire. Le cadre ne présente aucun défaut structurel. Une occasion rare à prix imbattable.",
            'description_de'   => "Dieses generalüberholte Trek Marlin 5 Klasse A wurde von unseren zertifizierten Technikern vollständig überholt. Antrieb, Bremsen und Lager wurden überprüft und bei Bedarf ersetzt. Der Rahmen weist keine strukturellen Mängel auf. Eine seltene Gelegenheit zum unschlagbaren Preis.",
            'price'            => 699.00,
            'sale_price'       => 549.00,
            'stock'            => 3,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Trek',
            'bike_type'        => 'VTT Hardtail (reconditionné)',
            'wheel_size'       => '29"',
            'frame_material'   => 'Aluminium Alpha',
            'number_of_speeds' => '21 vitesses Shimano',
            'suspension_type'  => 'SR Suntour 100 mm',
            'brake_style'      => 'V-Brake',
            'weight'           => '14,8 kg',
            'warranty_type'    => 'Garantie reconditionnement 6 mois',
            'warranty_description' => "Garantie 6 mois pièces et main d'œuvre sur tous les composants révisés.\nInclut : câblerie, transmission, freinage, roulements.\nExclus : usure normale (pneus, plaquettes).",
            'colors'           => 'Noir/Gris',
            'images'           => [
                'https://images.unsplash.com/photo-1544191696-102dbdaeeaa0?w=900&q=85',
                'https://images.unsplash.com/photo-1576435728678-68d0fbf94946?w=900&q=85',
            ],
        ]);

        $make([
            'cat'              => 'reconditionne',
            'name'             => 'Giant Defy Advanced 1 Route Reconditionné Grade B',
            'name_de'          => 'Giant Defy Advanced 1 Rennrad Generalüberholt Klasse B',
            'slug'             => 'giant-defy-advanced-1-reconditionne-grade-b',
            'description'      => "Ce Giant Defy Advanced 1 reconditionné Grade B présente de légères rayures esthétiques sur le cadre carbone, mais est fonctionnellement parfait. Sa transmission Shimano Ultegra a été entièrement révisée. Une opportunité exceptionnelle pour rouler en carbone à prix cassé.",
            'description_de'   => "Dieses generalüberholte Giant Defy Advanced 1 Klasse B weist leichte optische Kratzer am Carbon-Rahmen auf, ist aber funktionell einwandfrei. Das Shimano Ultegra Antriebssystem wurde vollständig überholt. Eine außergewöhnliche Gelegenheit, Carbon zum Spottpreis zu fahren.",
            'price'            => 1299.00,
            'sale_price'       => 899.00,
            'stock'            => 2,
            'is_featured'      => false,
            'is_new'           => false,
            'brand'            => 'Giant',
            'bike_type'        => 'Vélo de route (reconditionné)',
            'wheel_size'       => '700c',
            'frame_material'   => 'Carbone Advanced Grade',
            'number_of_speeds' => '22 vitesses Shimano Ultegra',
            'suspension_type'  => 'Rigide',
            'brake_style'      => 'Étriers Shimano Ultegra',
            'weight'           => '8,1 kg',
            'warranty_type'    => 'Garantie reconditionnement 6 mois',
            'colors'           => 'Noir/Rouge',
            'images'           => [
                'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=900&q=85',
            ],
        ]);

        $total = Product::count();
        $cats  = Category::count();
        $imgs  = ProductImage::count();

        $this->command->info("✅ Seeder terminé !");
        $this->command->info("   📂 {$cats} catégories");
        $this->command->info("   🚲 {$total} produits");
        $this->command->info("   🖼  {$imgs} images");
    }
}
