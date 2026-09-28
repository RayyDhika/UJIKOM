@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Proses Pengajuan Pengembalian')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
    @foreach ($errors->all() as $error)
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">{{ $error }}</div>
    @endforeach

    <!-- Info Peminjam & Alat -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-900"><span class="font-semibold">Peminjam:</span> {{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
        <p class="text-sm text-blue-900"><span class="font-semibold">Tgl Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}</p>
        <p class="text-sm text-blue-900"><span class="font-semibold">Rencana Kembali:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}</p>
        <p class="text-sm text-blue-900 mt-2 font-semibold">Alat yang diajukan untuk dikembalikan:</p>
        <ul class="list-disc list-inside text-sm text-blue-800 ml-2">
            @foreach($peminjaman->detailPinjam as $detail)
                <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} (Jumlah: {{ $detail->jumlah }})</li>
            @endforeach
        </ul>
    </div>

    <form id="form-proses-pengembalian">
        @csrf

        <!-- Dropdown Kondisi Alat (dipakai saat menyetujui) -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Dikembalikan</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak ringan" {{ old('kondisi_kembali') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak sedang" {{ old('kondisi_kembali') == 'rusak sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                <option value="rusak berat" {{ old('kondisi_kembali') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">Hanya diperlukan jika pengajuan disetujui.</p>
        </div>

        <!-- Denda -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Keterlambatan / Kerusakan (Rp) <span class="text-xs text-gray-400 font-normal">(Isi 0 jika tidak ada)</span></label>
            <input type="number" name="denda" value="{{ old('denda', 0) }}" min="0"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Catatan penolakan -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Catatan Penolakan <span class="text-xs text-gray-400 font-normal">(Ditampilkan ke peminjam jika ditolak)</span></label>
            <textarea name="catatan_pengembalian" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Alat belum dikembalikan secara fisik ke gudang."></textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.pengembalian.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>

            <div class="flex gap-2">
                <button type="submit"
                        formaction="{{ route('admin.pengembalian.tolak', $peminjaman->id) }}"
                        formnovalidate
                        onclick="return confirm('Yakin ingin menolak pengajuan pengembalian ini?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Tolak Pengembalian
                </button>
                <button type="submit"
                        formaction="{{ route('admin.pengembalian.setujui', $peminjaman->id) }}"
                        onclick="return confirm('Setujui pengembalian alat ini? Stok akan otomatis dipulihkan.')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Menyetujui
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Form dikirim via formaction ke route setujui/tolak yang berbeda,
    // keduanya method POST sehingga cukup satu <form> dengan dua tombol submit.
    document.getElementById('form-proses-pengembalian').setAttribute('method', 'POST');
</script>
@endsection
