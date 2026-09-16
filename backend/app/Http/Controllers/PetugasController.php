<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

   // Laporan
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
        'per_page' => [
            'nullable',
            'integer',
            'min:1',
            'max:100'
        ],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Parameter filter tidak valid.',
            'errors' => $validator->errors()
        ], 422);
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

    $perPage = $request->input('per_page', 15);

    $laporan = $query
        ->latest('tgl_pinjam')
        ->paginate($perPage);

    return view('petugas.laporan.index', [
    'laporan' => $laporan,
    'start_date' => $request->start_date,
    'end_date' => $request->end_date,
    'status' => $request->status,
]);

}

public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');
        
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

        return view('petugas.pengembalian.index', compact('pengembalians', 'search'));
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
        return response()->json([
            'message' => 'Parameter filter tidak valid.',
            'errors' => $validator->errors()
        ], 422);
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

    $pdf = Pdf::loadView('laporan.peminjaman', [
        'laporan' => $laporan,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
    ]);

    $pdf->setPaper('A4', 'landscape');

    return $pdf->stream('laporan-peminjaman.pdf');
}

    
}