@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Panel Admin')
@section('header-title', 'Form Tambah Transaksi Peminjaman')

@section('content')     
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('admin.peminjaman.store') }}" method="POST">
        @csrf

        {{-- PILIH PEMINJAM --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Peminjam (User)</label>
            <div class="relative" id="user-dropdown">

                {{-- Input Search --}}
                <input type="text" id="user-search" placeholder="Cari nama atau email..." autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                {{-- ID User yang dikirim ke Controller --}}
                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}" required>
                {{-- Dropdown User --}}
                <div id="user-options" class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                    @foreach($users as $user)
                        <button type="button" class="user-option w-full text-left px-3 py-2 hover:bg-blue-50 transition" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                            <div class="font-medium text-gray-800">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $user->email }}
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TANGGAL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            {{-- Tanggal Pinjam --}}
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}"required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Tanggal Kembali --}}
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+3 days'))) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        
        {{-- DAFTAR ALAT --}}
        <div class="mb-6">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Daftar Alat yang Dipinjam
            </label>

            <div id="alat-container" class="space-y-3">

                {{-- ROW ALAT PERTAMA --}}
                <div class="alat-row flex items-center gap-3">

                    {{-- Searchable Dropdown Alat --}}
                    <div class="relative flex-1 alat-dropdown">

                        {{-- Search --}}
                        <input
                            type="text"
                            placeholder="Cari alat..."
                            autocomplete="off"
                            class="alat-search w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >

                        {{-- ID Alat yang dikirim --}}
                        <input
                            type="hidden"
                            name="alat_id[]"
                            class="alat-id"
                            required
                        >

                        {{-- Dropdown --}}
                        <div
                            class="alat-options hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                        >

                            @foreach($alats as $alat)

                                <button
                                    type="button"
                                    class="alat-option w-full text-left px-3 py-2 hover:bg-blue-50 transition"
                                    data-id="{{ $alat->id }}"
                                    data-name="{{ $alat->nama_alat }}"
                                    data-stok="{{ $alat->stok }}"
                                >

                                    <div class="font-medium text-gray-800">
                                        {{ $alat->nama_alat }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        Stok: {{ $alat->stok }}
                                    </div>

                                </button>

                            @endforeach

                        </div>

                    </div>


                    {{-- Jumlah --}}
                    <input
                        type="number"
                        name="jumlah[]"
                        value="1"
                        min="1"
                        placeholder="Jumlah"
                        required
                        class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >


                    {{-- Tombol Hapus --}}
                    <button
                        type="button"
                        onclick="removeRow(this)"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 text-xs rounded-lg transition whitespace-nowrap"
                    >
                        Hapus
                    </button>

                </div>

            </div>


            {{-- Tambah Alat --}}
            <button
                type="button"
                onclick="addRow()"
                class="mt-3 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg transition"
            >
                + Tambah Alat Lain
            </button>

        </div>


        {{-- ============================= --}}
        {{-- BUTTON --}}
        {{-- ============================= --}}
        <div class="flex justify-end space-x-2">

            {{-- Batal --}}
            <a
                href="{{ route('admin.peminjaman.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition"
            >
                Batal
            </a>


            {{-- Simpan --}}
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
            >
                Simpan Peminjaman
            </button>

        </div>

    </form>

</div>


{{-- ================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    // =================================================
    // SEARCH PEMINJAM
    // =================================================

    const userSearch = document.getElementById('user-search');
    const userOptions = document.getElementById('user-options');
    const userId = document.getElementById('user_id');


    // Buka dropdown ketika input diklik
    userSearch.addEventListener('focus', function () {

        userOptions.classList.remove('hidden');

    });


    // Search user
    userSearch.addEventListener('input', function () {

        const keyword = this.value.toLowerCase().trim();

        userOptions.classList.remove('hidden');


        document.querySelectorAll('.user-option').forEach(option => {

            const name = option.dataset.name.toLowerCase();
            const email = option.dataset.email.toLowerCase();


            if (
                name.includes(keyword) ||
                email.includes(keyword)
            ) {

                option.classList.remove('hidden');

            } else {

                option.classList.add('hidden');

            }

        });

    });


    // Pilih user
    document.querySelectorAll('.user-option').forEach(option => {

        option.addEventListener('click', function () {

            userSearch.value =
                this.dataset.name +
                ' (' +
                this.dataset.email +
                ')';


            userId.value = this.dataset.id;


            userOptions.classList.add('hidden');

        });

    });



    // =================================================
    // SEARCH ALAT
    // =================================================

    function setupAlatDropdown(row) {

        const search = row.querySelector('.alat-search');

        const hiddenId = row.querySelector('.alat-id');

        const options = row.querySelector('.alat-options');


        // Buka dropdown
        search.addEventListener('focus', function () {

            options.classList.remove('hidden');

        });


        // Search alat
        search.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            options.classList.remove('hidden');


            row.querySelectorAll('.alat-option').forEach(option => {

                const name =
                    option.dataset.name.toLowerCase();


                if (name.includes(keyword)) {

                    option.classList.remove('hidden');

                } else {

                    option.classList.add('hidden');

                }

            });

        });


        // Pilih alat
        row.querySelectorAll('.alat-option').forEach(option => {

            option.addEventListener('click', function () {

                search.value =
                    this.dataset.name +
                    ' (Stok: ' +
                    this.dataset.stok +
                    ')';


                hiddenId.value =
                    this.dataset.id;


                options.classList.add('hidden');

            });

        });

    }



    // =================================================
    // AKTIFKAN SEARCH ALAT PERTAMA
    // =================================================

    const firstRow =
        document.querySelector('.alat-row');


    if (firstRow) {

        setupAlatDropdown(firstRow);

    }



    // =================================================
    // TAMBAH ROW ALAT
    // =================================================

    window.addRow = function () {

        const container =
            document.getElementById('alat-container');


        const firstRow =
            container.querySelector('.alat-row');


        const newRow =
            firstRow.cloneNode(true);


        // Reset search alat
        newRow.querySelector('.alat-search').value = '';


        // Reset ID alat
        newRow.querySelector('.alat-id').value = '';


        // Reset jumlah
        newRow.querySelector(
            'input[type="number"]'
        ).value = '1';


        // Tutup dropdown
        newRow.querySelector(
            '.alat-options'
        ).classList.add('hidden');


        // Masukkan row baru
        container.appendChild(newRow);


        // Aktifkan dropdown/search
        setupAlatDropdown(newRow);

    };



    // =================================================
    // HAPUS ROW ALAT
    // =================================================

    window.removeRow = function (button) {

        const rows =
            document.querySelectorAll('.alat-row');


        if (rows.length > 1) {

            button
                .closest('.alat-row')
                .remove();

        } else {

            alert(
                'Minimal harus ada 1 alat yang dipilih.'
            );

        }

    };



    // =================================================
    // KLIK DI LUAR DROPDOWN
    // =================================================

    document.addEventListener('click', function (event) {


        // Tutup dropdown user
        if (
            !event.target.closest('#user-dropdown')
        ) {

            userOptions.classList.add('hidden');

        }


        // Tutup dropdown alat
        document
            .querySelectorAll('.alat-dropdown')
            .forEach(dropdown => {

                if (
                    !event.target.closest('.alat-dropdown')
                ) {

                    dropdown
                        .querySelector('.alat-options')
                        .classList
                        .add('hidden');

                }

            });

    });

});

</script>

@endsection