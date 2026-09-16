@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Petugas')
@section('header-title', 'Pemantauan Pengembalian Alat')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Alat Kembali</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Form Search -->
                <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('petugas.pengembalian.index') }}"
                           class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                        <th class="py-3 px-4 border-b">Petugas Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($pengembalians as $pengembalian)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @if($pengembalian->peminjaman)
                                        @foreach($pengembalian->peminjaman->detailPinjam as $detail)
                                            <li>
                                                <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span> 
                                                (Jumlah: {{ $detail->jumlah }})
                                            </li>
                                        @endforeach
                                    @else
                                        <span class="text-red-500">Data peminjaman dihapus</span>
                                    @endif
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    {{ strtolower($pengembalian->kondisi_kembali) == 'baik' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($pengembalian->kondisi_kembali) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b text-red-600 font-semibold">
                                Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 border-b text-gray-600 text-xs font-medium">
                                {{ $pengembalian->petugas->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Belum ada alat yang dikembalikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $pengembalians->links() }}
        </div>
    </div>
@endsection