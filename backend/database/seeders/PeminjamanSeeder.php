<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-06-01',
                'tgl_kembali_plan' => '2026-06-05',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-07-10',
                'tgl_kembali_plan' => '2026-07-15',
                'status' => 'telat',
            ],
            [
                'user_id' => 3,
                'tgl_pinjam' => '2026-07-17',
                'tgl_kembali_plan' => '2026-07-20',
                'status' => 'dipinjam',
            ],
            [
                'user_id' => 4,
                'tgl_pinjam' => '2026-07-15',
                'tgl_kembali_plan' => '2026-07-16',
                'status' => 'diajukan',
            ],
        ];
        foreach($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}