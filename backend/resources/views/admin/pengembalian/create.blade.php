@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Panel Admin')
@section('header-title', 'Form Pengembalian Alat')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.pengembalian.store') }}" method="POST">
        @csrf
       <!-- Pilihan Transaksi Peminjaman -->
        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Pilih Transaksi Peminjaman (Status: Dipinjam)
            </label>

            <div class="relative" id="peminjaman-dropdown">

                <!-- Input Search -->
                <input
                    type="text"
                    id="peminjaman-search"
                    placeholder="Cari nama peminjam atau tanggal pinjam..."
                    autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <!-- ID Peminjaman yang dikirim ke Controller -->
                <input
                    type="hidden"
                    name="peminjaman_id"
                    id="peminjaman_id"
                    value="{{ old('peminjaman_id') }}"
                    required>

                <!-- Dropdown hasil pencarian -->
                <div
                    id="peminjaman-options"
                    class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                    @foreach($peminjamans as $pjm)

                        @php
                            $listAlat = '';

                            foreach($pjm->detailPinjam as $detail) {
                                $nama_alat = $detail->alat->nama_alat ?? 'Alat Dihapus';

                                $listAlat .= "- {$nama_alat} (Jumlah: {$detail->jumlah} pcs)<br>";
                            }

                            $namaPeminjam = $pjm->user->name ?? 'User Dihapus';

                            $tanggalPinjam = \Carbon\Carbon::parse($pjm->tgl_pinjam)
                                ->format('d M Y');
                        @endphp

                        <button
                            type="button"
                            class="peminjaman-option w-full text-left px-3 py-2 hover:bg-blue-50 transition"
                            data-id="{{ $pjm->id }}"
                            data-name="{{ $namaPeminjam }}"
                            data-date="{{ $tanggalPinjam }}"
                            data-alat="{{ $listAlat }}">

                            <div class="font-medium text-gray-800">
                                {{ $namaPeminjam }}
                            </div>

                            <div class="text-xs text-gray-500">
                                Tgl Pinjam: {{ $tanggalPinjam }}
                            </div>

                        </button>

                    @endforeach

                </div>
            </div>

            @error('peminjaman_id')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror

        </div>

        <!-- Info Box: Menampilkan Alat yang dipinjam secara otomatis -->
        <div id="info-alat-container" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
            <p class="text-sm font-semibold text-blue-800 mb-2">📋 Alat yang harus dikembalikan:</p>
            <div id="daftar-alat" class="text-sm text-blue-700 font-medium ml-2"></div>
        </div>

        <!-- Pilihan Petugas Penyetuju -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Petugas yang Menerima/Menyetujui</label>
            <select name="petugas_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Petugas --</option>
                @foreach($petugas as $p)
                    <option value="{{ $p->id }}" {{ old('petugas_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
            @error('petugas_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Dropdown Pilihan Kondisi Alat -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Dikembalikan</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak ringan" {{ old('kondisi_kembali') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak sedang" {{ old('kondisi_kembali') == 'rusak sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                <option value="rusak berat" {{ old('kondisi_kembali') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
            @error('kondisi_kembali') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Denda -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Keterlambatan / Kerusakan (Rp) <span class="text-xs text-gray-400 font-normal">(Isi 0 jika tidak ada)</span></label>
            <input type="number" name="denda" value="{{ old('denda', 0) }}" min="0" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('denda') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Simpan Pengembalian</button>
        </div>
    </form>
</div>

<!-- Script untuk Menampilkan Daftar Alat secara Dinamis -->
<script>

document.addEventListener('DOMContentLoaded', function () {

    // =================================================
    // SEARCH TRANSAKSI PEMINJAMAN
    // =================================================

    const peminjamanSearch =
        document.getElementById('peminjaman-search');

    const peminjamanOptions =
        document.getElementById('peminjaman-options');

    const peminjamanId =
        document.getElementById('peminjaman_id');


    // Buka dropdown ketika input diklik
    peminjamanSearch.addEventListener('focus', function () {

        peminjamanOptions.classList.remove('hidden');

    });


    // Search peminjaman
    peminjamanSearch.addEventListener('input', function () {

        const keyword =
            this.value.toLowerCase().trim();

        peminjamanOptions.classList.remove('hidden');

        document.querySelectorAll('.peminjaman-option').forEach(option => {

            const name =
                option.dataset.name.toLowerCase();

            const date =
                option.dataset.date.toLowerCase();

            if (
                name.includes(keyword) ||
                date.includes(keyword)
            ) {

                option.classList.remove('hidden');

            } else {

                option.classList.add('hidden');

            }

        });

    });


    // =================================================
    // PILIH TRANSAKSI PEMINJAMAN
    // =================================================

    document.querySelectorAll('.peminjaman-option').forEach(option => {

        option.addEventListener('click', function () {

            const nama =
                this.dataset.name;

            const tanggal =
                this.dataset.date;

            const id =
                this.dataset.id;

            const alatData =
                this.dataset.alat;


            // Tampilkan data pada search bar
            peminjamanSearch.value =
                nama + ' - Tgl Pinjam: ' + tanggal;


            // Simpan ID peminjaman
            peminjamanId.value = id;


            // Tutup dropdown
            peminjamanOptions.classList.add('hidden');


            // Tampilkan daftar alat
            showAlatDetails(alatData);

        });

    });


    // =================================================
    // TAMPILKAN ALAT
    // =================================================

    function showAlatDetails(alatData) {

        const container =
            document.getElementById('info-alat-container');

        const daftarAlat =
            document.getElementById('daftar-alat');


        if (alatData) {

            daftarAlat.innerHTML = alatData;

            container.classList.remove('hidden');

        } else {

            container.classList.add('hidden');

            daftarAlat.innerHTML = '';

        }

    }


    // =================================================
    // KLIK DI LUAR DROPDOWN
    // =================================================

    document.addEventListener('click', function (event) {

        if (!event.target.closest('#peminjaman-dropdown')) {

            peminjamanOptions.classList.add('hidden');

        }

    });


    // =================================================
    // OLD VALUE / VALIDATION ERROR
    // =================================================

    const oldId =
        peminjamanId.value;

    if (oldId) {

        const selected =
            document.querySelector(
                '.peminjaman-option[data-id="' + oldId + '"]'
            );

        if (selected) {

            const nama =
                selected.dataset.name;

            const tanggal =
                selected.dataset.date;

            const alatData =
                selected.dataset.alat;


            peminjamanSearch.value =
                nama + ' - Tgl Pinjam: ' + tanggal;


            showAlatDetails(alatData);

        }

    }

});
</script>
@endsection