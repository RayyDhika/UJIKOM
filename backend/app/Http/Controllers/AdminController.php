<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Wajib ditambahkan untuk enkripsi password
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPinjam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Halaman dashboard admin: ringkasan + log aktivitas terbaru
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(20)->get();

        return view('admin.dashboard', compact('logs'));
    }

    // FITUR KELOLA USER

    // Daftar user (dengan fitur pencarian dan paginasi)
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('role', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10) // Tampilkan 10 data per halaman
        ->withQueryString(); // Memastikan parameter search tetap ada saat pindah halaman

        return view('admin.user.index', compact('users', 'search'));
    }

    // Menampilkan form tambah user baru
    public function createUser()
    {
        return view('admin.user.create');
    }

    // Menyimpan user baru ke database
    public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,petugas,peminjam',
        'no_hp' => 'nullable|string|max:20',
        'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'no_hp' => $request->no_hp,
    ];

    // Upload foto profil
    if ($request->hasFile('foto_profile')) {

        $folder = public_path('storage/profile');

        // Buat folder jika belum ada
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $file = $request->file('foto_profile');

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($folder, $filename);

        // Simpan lokasi file ke database
        $data['foto_profile'] = 'storage/profile/' . $filename;
    }

    User::create($data);

    return redirect()
        ->route('admin.user.index')
        ->with('success', 'User berhasil ditambahkan.');
}

    // Menampilkan form edit data user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    // Memperbarui data user
   public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'role' => 'required|in:admin,petugas,peminjam',
        'no_hp' => 'nullable|string|max:20',
        'password' => 'nullable|string|min:6',
        'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'no_hp' => $request->no_hp,
    ];

    // Jika password diisi, ubah password
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Jika user mengupload foto baru
    if ($request->hasFile('foto_profile')) {

        $folder = public_path('storage/profile');

        // Buat folder jika belum ada
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        // Hapus foto lama jika ada
        if ($user->foto_profile) {
            $oldPhoto = public_path($user->foto_profile);

            if (file_exists($oldPhoto)) {
                unlink($oldPhoto);
            }
        }

        // Upload foto baru
        $file = $request->file('foto_profile');

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($folder, $filename);

        // Simpan lokasi foto baru
        $data['foto_profile'] = 'storage/profile/' . $filename;
    }

    $user->update($data);

    return redirect()
        ->route('admin.user.index')
        ->with('success', 'Data user berhasil diperbarui.');
}

    // Menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // FITUR KELOLA KATEGORI
    
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query,  $search) {
            // Perbaikan: menambahkan penutup kurung kurawal '}' di akhir variabel $search
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru 
    public function storeKategori(Request $request)
    {
        $request->validate([
            // Perbaikan: Mengganti backslash (\) menjadi pipe (|)
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // Perbaikan: Mengganti 'succes' menjadi 'success'
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,'. $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    // 6. Menghapust Kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        //Cek apakah kategori masih dipakai oleh alat
        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
            ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    //crud alat
    // 1. Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }
    
    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Update Gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        // Hapus file gambar fisik jika ada
        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    //crud peminjaman
    //1. menampilkan daftar peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. menampilkan form tambah peminjam
    public function createPeminjaman() 
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. menyimpan data peminjam baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            //buat transaksi utama peminjam
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);
            
            //simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                //validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);

                //kurangi stok alat jika status langsung disetujui/dipinjam  (optional atau dikurangi saat status berubah jadi dipinjam)
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }

    }

    // 4. memperbarui status peminjaman 
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,dikembalikan,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            //logika pengelolaan stok otomatis
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                //kurangi stok karena barang resmi dipinjam
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && ($statusBaru == 'dikembalikan')) {
                //kembalikan stok karena barang sudah dikembalikan
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);
            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    //menghapus data peminjam
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

        // jika statusnya sedang dipinjam kembalikan stok terlebih dahulu sebelum dihapus
        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

// ==========================================
// CRUD PENGEMBALIAN
// ==========================================

public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');
        
        $pengembalians = \App\Models\Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($search, function ($query, $search) {
                // Pencarian berdasarkan nama peminjam, nama petugas, atau kondisi
                return $query->whereHas('peminjaman.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('petugas', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('kondisi_kembali', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('pengembalians', 'search'));
    }

    // Menampilkan form tambah pengembalian
    public function createPengembalian()
    {
        // PERBAIKAN: Menggunakan detailPinjam
        $peminjamans = \App\Models\Peminjaman::with(['user', 'detailPinjam.alat'])->where('status', 'dipinjam')->get();
        
        $petugas = \App\Models\User::where('role', 'petugas')->get();
                        
        return view('admin.pengembalian.create', compact('peminjamans', 'petugas'));
    }

    // Menyimpan data pengembalian & mengembalikan stok alat
    public function storePengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'petugas_id' => 'required|exists:users,id',
            'kondisi_kembali' => 'required|in:baik,rusak ringan,rusak sedang,rusak berat',
            'denda' => 'nullable|integer|min:0',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // PERBAIKAN: Menggunakan detailPinjam
            $peminjaman = \App\Models\Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);

            \App\Models\Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(), 
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => $request->petugas_id, 
            ]);

            // Status berubah ke selesai
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan stok alat (PERBAIKAN: pakai detailPinjam)
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = \App\Models\Alat::findOrFail($detail->alat_id);
                $alat->increment('stok', $detail->jumlah);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.pengembalian.index')
                             ->with('success', 'Data pengembalian berhasil dicatat dan stok dipulihkan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyPengembalian($id)
    {
        $pengembalian = \App\Models\Pengembalian::findOrFail($id);

        if ($pengembalian->peminjaman) {
            $pengembalian->peminjaman->update(['status' => 'dipinjam']);
        }
        
        $pengembalian->delete();

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }
}