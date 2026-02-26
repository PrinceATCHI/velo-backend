<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $adminRole = Role::create(['name' => 'admin']);
        $customerRole = Role::create(['name' => 'customer']);

        // Créer un admin
        $admin = User::create([
            'name' => 'Admin Fahrrad',
            'email' => 'admin@fahrrad.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);

        // Créer un client test
        $customer = User::create([
            'name' => 'Jean Dupont',
            'email' => 'client@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $customer->assignRole($customerRole);

        // Créer 5 clients supplémentaires
        User::factory(5)->create()->each(function($user) use ($customerRole) {
            $user->assignRole($customerRole);
        });
    }
}