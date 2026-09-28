<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Diisi saat peminjam mengajukan pengembalian alat, dikosongkan lagi
            // saat pengajuan disetujui/ditolak/dibatalkan.
            $table->timestamp('pengembalian_diajukan_at')->nullable()->after('status');

            // Diisi saat admin/petugas menolak pengajuan pengembalian, supaya
            // peminjam bisa melihat alasan penolakannya di halaman riwayat.
            $table->text('catatan_pengembalian')->nullable()->after('pengembalian_diajukan_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['pengembalian_diajukan_at', 'catatan_pengembalian']);
        });
    }
};
