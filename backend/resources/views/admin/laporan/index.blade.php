@extends('layouts.app')

@section('title', 'Cetak Laporan - Panel Admin')
@section('header-title', 'Cetak Laporan Peminjaman Alat')

@section('content')

{{-- Filter --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 mb-5">

    <div class="p-5 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Filter Laporan</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Pilih estimasi waktu dan kondisi, lalu klik <strong>Tampilkan</strong> untuk melihat datanya.
                </p>
            </div>

            @if($sudahTampil)
                <a href="{{ route('admin.laporan.cetak', request()->query()) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2zm0-12h6" />
                    </svg>
                    Cetak PDF
                </a>
            @endif
        </div>
    </div>

    <div class="p-5">
        <form action="{{ route('admin.laporan.index') }}" method="GET"
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <input type="hidden" name="tampilkan" value="1">

            {{-- Estimasi Waktu --}}
            <div>
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Waktu (Mulai)</label>
                <input type="date" id="start_date" name="start_date" value="{{ $start_date }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Waktu (Sampai)</label>
                <input type="date" id="end_date" name="end_date" value="{{ $end_date }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- Kondisi / Status --}}
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi</label>
                <select id="status" name="status"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kondisi</option>
                    <option value="diajukan" {{ $status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dipinjam" {{ $status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ $status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="telat" {{ $status == 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>

            {{-- Tombol --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-normal rounded-lg transition shadow-sm">
                    Tampilkan
                </button>

                <a href="{{ route('admin.laporan.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 text-sm font-normal rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Laporan: hanya tampil setelah klik "Tampilkan" --}}
@if(!$sudahTampil)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-10 text-center text-gray-500">
        Silakan pilih estimasi waktu dan kondisi di atas, lalu klik <strong class="mx-1">Tampilkan</strong> untuk melihat data laporan.
    </div>
@else
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Daftar Laporan Peminjaman</h3>
            <p class="text-sm text-gray-500 mt-1">
                @if($start_date || $end_date || $status)
                    Menampilkan hasil berdasarkan filter yang dipilih.
                @else
                    Menampilkan seluruh data peminjaman.
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b text-center">No</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b text-center">Status</th>
                        <th class="py-3 px-4 border-b">Petugas Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($laporan as $index => $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach($item->detailPinjam as $detail)
                                        <li>
                                            <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-gray-500">(Jumlah: {{ $detail->jumlah }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-center">
                                @if($item->status === 'diajukan')
                                    <span class="inline-block text-xs font-semibold text-yellow-700 bg-yellow-50 px-2.5 py-1 rounded">Diajukan</span>
                                @elseif($item->status === 'dipinjam')
                                    <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">Dipinjam</span>
                                @elseif($item->status === 'dikembalikan')
                                    <span class="inline-block text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded">Dikembalikan</span>
                                @elseif($item->status === 'telat')
                                    <span class="inline-block text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded">Telat</span>
                                @else
                                    <span class="inline-block text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded">{{ ucfirst($item->status ?? '-') }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">{{ $item->pengembalian->petugas->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">Tidak ada data laporan peminjaman berdasarkan filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($laporan, 'hasPages') && $laporan->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $laporan->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
