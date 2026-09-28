<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }
            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan.' . $e->getMessage());
        }
    }

// laporan

   // Laporan: tabel hanya tampil setelah petugas klik "Tampilkan"
public function indexLaporan(Request $request)
{
    $validator = Validator::make($request->all(), [
        'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        'end_date' => [
            'nullable',
            'date',
            'date_format:Y-m-d',
            'after_or_equal:start_date'
        ],
        'status' => [
            'nullable',
            'string',
            'in:diajukan,dipinjam,dikembalikan,telat'
        ],
    ]);

    if ($validator->fails()) {
        return redirect()->route('petugas.laporan.index')->withErrors($validator)->withInput();
    }

    $sudahTampil = $request->boolean('tampilkan');
    $laporan = collect();

    if ($sudahTampil) {
        $query = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas'
        ]);

        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->whereDate('tgl_pinjam', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->whereDate('tgl_pinjam', '<=', $request->end_date);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $laporan = $query->latest('tgl_pinjam')->paginate(15)->withQueryString();
    }

    return view('petugas.laporan.index', [
        'laporan' => $laporan,
        'sudahTampil' => $sudahTampil,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
    ]);
}

public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        // Daftar pengajuan pengembalian dari peminjam yang menunggu diproses
        $pendingPengembalian = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->menungguPengembalian()
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->oldest('pengembalian_diajukan_at')
            ->get();

        // Mengambil data pengembalian beserta relasinya
        // Catatan: Tetap menggunakan 'detailPinjam' sesuai dengan model kamu
        $pengembalians = \App\Models\Pengembalian::with(['peminjaman.user', 'petugas', 'peminjaman.detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama peminjam atau kondisi alat
                return $query->whereHas('peminjaman.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('kondisi_kembali', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.pengembalian.index', compact('pendingPengembalian', 'pengembalians', 'search'));
    }

    // Menampilkan form proses (setujui/tolak) pengajuan pengembalian dari peminjam
    public function prosesPengembalianForm($peminjamanId)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->menungguPengembalian()
            ->findOrFail($peminjamanId);

        return view('petugas.pengembalian.proses', compact('peminjaman'));
    }

    // Menyetujui pengajuan pengembalian: catat data pengembalian & pulihkan stok alat
    public function setujuiPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak ringan,rusak sedang,rusak berat',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->menungguPengembalian()->findOrFail($peminjamanId);

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            $peminjaman->update([
                'status' => 'dikembalikan',
                'pengembalian_diajukan_at' => null,
                'catatan_pengembalian' => null,
            ]);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->increment('stok', $detail->jumlah);
            }

            DB::commit();
            return redirect()->route('petugas.pengembalian.index')
                ->with('success', 'Pengembalian disetujui dan stok alat berhasil dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak pengajuan pengembalian dari peminjam
    public function tolakPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'catatan_pengembalian' => 'nullable|string|max:255',
        ]);

        $peminjaman = Peminjaman::menungguPengembalian()->findOrFail($peminjamanId);

        $peminjaman->update([
            'pengembalian_diajukan_at' => null,
            'catatan_pengembalian' => $request->catatan_pengembalian
                ?: 'Pengajuan pengembalian ditolak oleh petugas.',
        ]);

        return redirect()->route('petugas.pengembalian.index')
            ->with('success', 'Pengajuan pengembalian berhasil ditolak.');
    }

public function cetakLaporan(Request $request)
{
    $validator = Validator::make($request->all(), [
        'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        'end_date' => [
            'nullable',
            'date',
            'date_format:Y-m-d',
            'after_or_equal:start_date'
        ],
        'status' => [
            'nullable',
            'string',
            'in:diajukan,dipinjam,dikembalikan,telat'
        ],
    ]);

    if ($validator->fails()) {
        return redirect()->route('petugas.laporan.index')->withErrors($validator)->withInput();
    }

    $query = Peminjaman::with([
        'user',
        'detailPinjam.alat',
        'pengembalian.petugas'
    ]);

    // Filter tanggal
    $query->when(
        $request->filled('start_date'),
        function ($q) use ($request) {
            $q->whereDate('tgl_pinjam', '>=', $request->start_date);
        }
    );

    $query->when(
        $request->filled('end_date'),
        function ($q) use ($request) {
            $q->whereDate('tgl_pinjam', '<=', $request->end_date);
        }
    );

    // Filter status
    $query->when(
        $request->filled('status'),
        function ($q) use ($request) {
            $q->where('status', $request->status);
        }
    );

    // Untuk cetak jangan pakai paginate
    $laporan = $query
        ->latest('tgl_pinjam')
        ->get();

    $pdf = Pdf::loadView('laporan.cetak', [
        'laporan' => $laporan,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
    ]);

    $pdf->setPaper('A4', 'landscape');

    return $pdf->stream('laporan-peminjaman.pdf');
}

    
}