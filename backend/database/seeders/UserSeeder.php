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
                'name' => 'Admin Amarta',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1123'),
                'role' => 'admin',
                'no_hp' => '085624745987',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'Petugas Deja',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'no_hp' => '0856224745986',
                'alamat' => 'Baleendah, Bandung',
            ],
            [
                'name' => 'Rayy Dhikaa',
                'email' => 'rayy@gmail.com',
                'password' => Hash::make('dhika123'),
                'role' => 'peminjam',
                'no_hp' => '085624745985',
                'alamat' => 'Bandung, West Java',
            ],
            [
                 'name' => 'Mardhika Raisya',
                'email' => 'mardhika@gmail.com',
                'password' => Hash::make('mardhika123'),
                'role' => 'peminjam',
                'no_hp' => '085624745983',
                'alamat' => 'Bandung, West Java',
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}