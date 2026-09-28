@extends('layouts.app')

@section('title', 'Dashboard Admin – Sistem Peminjaman')
@section('header-title', 'Ringkasan Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- Kartu Ringkasan per Menu -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">

        <!-- Kelola User -->
        <a href="{{ route('admin.user.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Kelola User</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['user']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-bold">U</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                Admin: {{ $stats['user']['admin'] }} &middot;
                Petugas: {{ $stats['user']['petugas'] }} &middot;
                Peminjam: {{ $stats['user']['peminjam'] }}
            </p>
        </a>

        <!-- Kelola Kategori -->
        <a href="{{ route('admin.kategori.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-purple-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Kelola Kategori</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['kategori']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xl font-bold">K</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Total kategori alat terdaftar</p>
        </a>

        <!-- Kelola Alat -->
        <a href="{{ route('admin.alat.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-amber-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Kelola Alat</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['alat']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xl font-bold">A</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Total stok tersedia: {{ $stats['alat']['total_stok'] }} unit</p>
        </a>

        <!-- Kelola Peminjaman -->
        <a href="{{ route('admin.peminjaman.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-indigo-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Kelola Peminjaman</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['peminjaman']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold">P</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                Diajukan: {{ $stats['peminjaman']['diajukan'] }} &middot;
                Dipinjam: {{ $stats['peminjaman']['dipinjam'] }} &middot;
                Kembali: {{ $stats['peminjaman']['dikembalikan'] }} &middot;
                Telat: {{ $stats['peminjaman']['telat'] }}
            </p>
        </a>

        <!-- Kelola Pengembalian -->
        <a href="{{ route('admin.pengembalian.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-emerald-300 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Kelola Pengembalian</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pengembalian']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl font-bold">R</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                @if($stats['pengembalian']['menunggu'] > 0)
                    <span class="text-amber-600 font-semibold">{{ $stats['pengembalian']['menunggu'] }} menunggu persetujuan</span>
                @else
                    Tidak ada pengajuan yang menunggu
                @endif
            </p>
        </a>

        <!-- Log Aktivitas -->
        <a href="{{ route('admin.logAktivitas.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-400 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Log Aktivitas</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['log_aktivitas']['total'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xl font-bold">L</div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Lihat seluruh riwayat aktivitas &rarr;</p>
        </a>

    </div>

    <!-- Cetak Laporan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-md font-bold text-gray-800">Butuh laporan peminjaman?</h3>
            <p class="text-sm text-gray-500 mt-1">Filter berdasarkan estimasi waktu dan kondisi, lalu cetak dalam format PDF.</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}"
            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap">
            Buka Cetak Laporan
        </a>
    </div>
@endsection
