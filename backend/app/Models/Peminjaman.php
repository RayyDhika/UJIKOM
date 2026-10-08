<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Pengembalian;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
        'pengembalian_diajukan_at',
        'catatan_pengembalian',
    ];

    protected function casts(): array
    {
        return [
            'pengembalian_diajukan_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    // Tarif denda keterlambatan per hari (Rp)
    public const DENDA_TELAT_PER_HARI = 2000;

    /**
     * Jumlah hari keterlambatan dibanding rencana kembali (0 jika tidak telat).
     * Hari 1 telat = 1, hari ke-2 = 2, dst.
     */
    public function hitungHariTelat($tglKembali = null): int
    {
        $rencana = \Carbon\Carbon::parse($this->tgl_kembali_plan)->startOfDay();
        $kembali = ($tglKembali ? \Carbon\Carbon::parse($tglKembali) : now())->startOfDay();

        return $kembali->greaterThan($rencana) ? (int) $rencana->diffInDays($kembali) : 0;
    }

    /**
     * Denda keterlambatan otomatis: Rp 2.000 x jumlah hari telat.
     */
    public function hitungDendaTelat($tglKembali = null): int
    {
        return $this->hitungHariTelat($tglKembali) * self::DENDA_TELAT_PER_HARI;
    }

    /**
     * Peminjaman yang sedang dipinjam dan menunggu persetujuan pengembalian
     * dari admin/petugas.
     */
    public function scopeMenungguPengembalian($query)
    {
        return $query->where('status', 'dipinjam')->whereNotNull('pengembalian_diajukan_at');
    }
}