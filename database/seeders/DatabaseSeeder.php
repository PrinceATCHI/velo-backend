<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clés étrangères temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ═══════════════════════════════════════════════════════════
        // 1️⃣ CRÉER LES RÔLES ET PERMISSIONS
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('');
        $this->command->info('🔄 Création des rôles et permissions...');
        
        // Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $this->command->info('✅ Rôles créés : admin, customer');

        // Créer les permissions
        $permissions = [
            // Produits
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Commandes
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            
            // Utilisateurs
            'view users',
            'edit users',
            'delete users',
            
            // Autres
            'manage coupons',
            'manage payments',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $this->command->info('✅ ' . count($permissions) . ' permissions créées');

        // Assigner toutes les permissions à l'admin
        $adminRole->syncPermissions(Permission::all());

        // Assigner certaines permissions au customer
        $customerRole->syncPermissions([
            'view products',
            'create orders',
            'view orders',
        ]);

        $this->command->info('✅ Permissions assignées aux rôles');

        // ═══════════════════════════════════════════════════════════
        // 2️⃣ CRÉER LES UTILISATEURS PAR DÉFAUT
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('');
        $this->command->info('🔄 Création des utilisateurs...');

        // Créer l'admin principal
        $admin = User::updateOrCreate(
            ['email' => 'admin@fahrradhauskauf.de'],
            [
                'name' => 'Admin Fahrrad',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Supprimer tous les rôles existants puis assigner admin
        $admin->syncRoles([]);
        $admin->assignRole('admin');

        $this->command->info('✅ Admin créé : admin@fahrradhauskauf.de / password');

        // Créer un client de test
        $customer = User::updateOrCreate(
            ['email' => 'client@test.de'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Supprimer tous les rôles existants puis assigner customer
        $customer->syncRoles([]);
        $customer->assignRole('customer');

        $this->command->info('✅ Client créé : client@test.de / password');

        // Créer quelques clients supplémentaires pour les tests
        $additionalCustomers = [
            [
                'name' => 'Marie Dupont',
                'email' => 'marie@test.fr',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Hans Mueller',
                'email' => 'hans@test.de',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sophie Martin',
                'email' => 'sophie@test.fr',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($additionalCustomers as $customerData) {
            $newCustomer = User::updateOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'password' => $customerData['password'],
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );

            $newCustomer->syncRoles([]);
            $newCustomer->assignRole('customer');
        }

        $this->command->info('✅ ' . count($additionalCustomers) . ' clients supplémentaires créés');

        // ═══════════════════════════════════════════════════════════
        // 3️⃣ AUTRES SEEDERS (si tu en as)
        // ═══════════════════════════════════════════════════════════
        
        // Décommente si tu as d'autres seeders
        // $this->call([
        //     CategorySeeder::class,
        //     ProductSeeder::class,
        //     CouponSeeder::class,
        // ]);

        // Réactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ═══════════════════════════════════════════════════════════
        // 🎉 RÉSUMÉ FINAL
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('🎉 SEEDING TERMINÉ AVEC SUCCÈS !');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 STATISTIQUES :');
        $this->command->info('   • ' . Role::count() . ' rôles créés');
        $this->command->info('   • ' . Permission::count() . ' permissions créées');
        $this->command->info('   • ' . User::count() . ' utilisateurs créés');
        $this->command->info('');
        $this->command->info('📝 COMPTES DE TEST :');
        $this->command->info('');
        $this->command->info('   👨‍💼 ADMIN :');
        $this->command->info('      Email    : admin@fahrradhauskauf.de');
        $this->command->info('      Password : password');
        $this->command->info('      Rôle     : admin');
        $this->command->info('');
        $this->command->info('   👤 CLIENT 1 :');
        $this->command->info('      Email    : client@test.de');
        $this->command->info('      Password : password');
        $this->command->info('      Rôle     : customer');
        $this->command->info('');
        $this->command->info('   👤 CLIENT 2 :');
        $this->command->info('      Email    : marie@test.fr');
        $this->command->info('      Password : password');
        $this->command->info('');
        $this->command->info('   👤 CLIENT 3 :');
        $this->command->info('      Email    : hans@test.de');
        $this->command->info('      Password : password');
        $this->command->info('');
        $this->command->info('   👤 CLIENT 4 :');
        $this->command->info('      Email    : sophie@test.fr');
        $this->command->info('      Password : password');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('💡 PROCHAINES ÉTAPES :');
        $this->command->info('   1. Teste la connexion : php artisan serve');
        $this->command->info('   2. Ouvre http://localhost:8000');
        $this->command->info('   3. Connecte-toi avec un compte de test');
        $this->command->info('');
        $this->command->info('🔐 SÉCURITÉ :');
        $this->command->info('   ⚠️  Change le mot de passe admin en production !');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');
    }
}