<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanObserver
{

    public function catatLog(string $pesan) : void 
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => $pesan,
            ]);
        }
    }
    /**
     * Handle the Peminjaman "created" event.
     */
    public function created(Peminjaman $peminjaman): void
    {
        $namaPeminjam = $peminjaman->user?->name ?? 'User',
        $this->catatLog("Peminjaman ({$namaPeminjaman}) membuat permohonan peminjaman baru (ID: #{$peminjaman->id})");
    }

    /**
     * Handle the Peminjaman "updated" event.
     */
    public function updated(Peminjaman $peminjaman): void
    {
        if ($peminjaman->wasChanged('status')) {
            $this->catatLog("Status peminjaman (ID: #{$peminjaman->id}) berubah menjadi: '{$peminjaman->status}'");
        } else {
            if (!empty($peminjaman->getChanges())) {
                $this->catatLog("Memperbarui detail data peminjaman (ID: #{$peminjaman->id})");
            }
        }
    }

    /**
     * Handle the Peminjaman "deleted" event.
     */
    public function deleted(Peminjaman $peminjaman): void
    {
        $this->catatLog("Membatalkan/menghapus permohonan peminjaman (ID: #{$peminjaman->id})");
    }

    /**
     * Handle the Peminjaman "restored" event.
     */
    public function restored(Peminjaman $peminjaman): void
    {
        //
    }

    /**
     * Handle the Peminjaman "force deleted" event.
     */
    public function forceDeleted(Peminjaman $peminjaman): void
    {
        //
    }
}
