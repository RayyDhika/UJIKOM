<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Leon Scott Kennedy',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_hp' => '085624745987',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'Ashley Graham',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'no_hp' => '0856224745986',
                'alamat' => 'Baleendah, Bandung',
            ],
            [
                'name' => 'Claire Redfield',
                'email' => 'peminjam1@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '085624745985',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'Bill Williamson',
                'email' => 'peminjam2@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '085624745983',
                'alamat' => 'Bandung, West Java',
            ],
        ];
        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}