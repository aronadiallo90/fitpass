<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super admin
        User::firstOrCreate(
            ['phone' => '+221700000001'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@fitpass.sn',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
            ]
        );

        // Admin
        User::firstOrCreate(
            ['phone' => '+221700000002'],
            [
                'name'     => 'Admin FitPass',
                'email'    => 'admin@fitpass.sn',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Gym owners
        User::firstOrCreate(
            ['phone' => '+221770000010'],
            [
                'name'     => 'Mamadou Diallo',
                'email'    => 'mamadou@idealgym.sn',
                'password' => Hash::make('password'),
                'role'     => 'gym_owner',
            ]
        );

        User::firstOrCreate(
            ['phone' => '+221770000011'],
            [
                'name'     => 'Fatou Seck',
                'email'    => 'fatou@fitnessdakar.sn',
                'password' => Hash::make('password'),
                'role'     => 'gym_owner',
            ]
        );

        // Members
        $members = [
            ['name' => 'Awa Diop',     'phone' => '+221771234567', 'email' => 'awa@test.sn'],
            ['name' => 'Moussa Fall',  'phone' => '+221772345678', 'email' => 'moussa@test.sn'],
            ['name' => 'Aissatou Ba',  'phone' => '+221773456789', 'email' => 'aissatou@test.sn'],
            ['name' => 'Omar Ndiaye',  'phone' => '+221774567890', 'email' => 'omar@test.sn'],
            ['name' => 'Khady Gueye', 'phone' => '+221775678901', 'email' => 'khady@test.sn'],
        ];

        foreach ($members as $member) {
            User::firstOrCreate(
                ['phone' => $member['phone']],
                array_merge($member, [
                    'password' => Hash::make('password'),
                    'role'     => 'member',
                ])
            );
        }
    }
}
