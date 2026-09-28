<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Masukkan daftar alat yang dipinjam ke detail_pinjam
            foreach ($request->alat_id as $index => $alatId) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }

    // Membatalkan/menghapus pengajuan peminjaman yang belum disetujui (status: diajukan)
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Pengajuan peminjaman ini sudah diproses dan tidak dapat dihapus.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->detailPinjam()->delete();
            $peminjaman->delete();
        });

        return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil dihapus.');
    }

    // Peminjam mengajukan pengembalian alat yang sedang dipinjam (menunggu persetujuan admin/petugas)
    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Alat ini tidak dapat diajukan pengembaliannya karena statusnya bukan sedang dipinjam.');
        }

        if (!is_null($peminjaman->pengembalian_diajukan_at)) {
            return redirect()->back()->with('error', 'Pengajuan pengembalian untuk alat ini sudah dikirim dan sedang menunggu persetujuan.');
        }

        $peminjaman->update([
            'pengembalian_diajukan_at' => now(),
            'catatan_pengembalian' => null,
        ]);

        return redirect()->back()->with('success', 'Pengajuan pengembalian berhasil dikirim, menunggu persetujuan admin/petugas.');
    }

    // Peminjam membatalkan pengajuan pengembalian yang masih menunggu persetujuan
    public function batalkanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'dipinjam' || is_null($peminjaman->pengembalian_diajukan_at)) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan pengembalian yang bisa dibatalkan untuk data ini.');
        }

        $peminjaman->update([
            'pengembalian_diajukan_at' => null,
            'catatan_pengembalian' => null,
        ]);

        return redirect()->back()->with('success', 'Pengajuan pengembalian berhasil dibatalkan.');
    }
}