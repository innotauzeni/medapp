<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'superadmin@me.local'],
            [
                'name'              => 'ER Medics Administrator',
                'password'          => Hash::make('Admin12345!'),
                'phone'             => '+27 11 000 0000',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        $account = User::updateOrCreate(
            ['email' => 'account@me.local'],
            [
                'name'              => 'Account Officer',
                'password'          => Hash::make('Account12345!'),
                'phone'             => '+27 11 000 0001',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $account->assignRole('Account');
    }
}
