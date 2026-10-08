<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            // Kolom `denda` tetap menjadi TOTAL (denda_telat + denda_rusak)
            $table->unsignedInteger('hari_telat')->default(0)->after('kondisi_kembali');
            $table->unsignedInteger('denda_telat')->default(0)->after('hari_telat');
            $table->unsignedInteger('denda_rusak')->default(0)->after('denda_telat');
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['hari_telat', 'denda_telat', 'denda_rusak']);
        });
    }
};
