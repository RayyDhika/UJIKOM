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

    /**
     * Peminjaman yang sedang dipinjam dan menunggu persetujuan pengembalian
     * dari admin/petugas.
     */
    public function scopeMenungguPengembalian($query)
    {
        return $query->where('status', 'dipinjam')->whereNotNull('pengembalian_diajukan_at');
    }
}