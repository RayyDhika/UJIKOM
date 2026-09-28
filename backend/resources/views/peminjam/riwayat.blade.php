<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pinjam – Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('peminjam.katalog') }}">Panel Peminjam</a>
            <div class="d-flex">
                <a href="{{ route('peminjam.katalog') }}" class="btn btn-outline-light btn-sm me-2">Katalog Alat</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h3 class="mb-3">Riwayat Peminjaman Saya</h3>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Detail Alat</th>
                                <th>Status</th>
                                <th style="min-width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjamans as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}</td>
                                    <td>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($item->detailPinjam as $detail)
                                                <li>
                                                    <strong>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</strong>
                                                    (Jumlah: {{ $detail->jumlah }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if($item->status === 'diajukan')
                                            <span class="badge bg-secondary">Menunggu Persetujuan Pinjam</span>
                                        @elseif($item->status === 'dipinjam' && $item->pengembalian_diajukan_at)
                                            <span class="badge bg-warning text-dark">Menunggu Persetujuan Pengembalian</span>
                                        @elseif($item->status === 'dipinjam')
                                            <span class="badge bg-primary">Sedang Dipinjam</span>
                                        @elseif($item->status === 'dikembalikan')
                                            <span class="badge bg-success">Sudah Dikembalikan</span>
                                        @elseif($item->status === 'telat')
                                            <span class="badge bg-danger">Telat</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($item->status) }}</span>
                                        @endif

                                        @if($item->status === 'dikembalikan' && $item->pengembalian)
                                            <div class="small text-muted mt-1">
                                                Kondisi: {{ ucfirst($item->pengembalian->kondisi_kembali) }}<br>
                                                Denda: Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                                            </div>
                                        @endif

                                        @if($item->catatan_pengembalian)
                                            <div class="alert alert-danger py-1 px-2 mt-2 mb-0 small">
                                                <strong>Pengajuan pengembalian ditolak:</strong>
                                                {{ $item->catatan_pengembalian }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'diajukan')
                                            <form action="{{ route('peminjam.peminjaman.destroy', $item->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus pengajuan peminjaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                            </form>
                                        @elseif($item->status === 'dipinjam' && $item->pengembalian_diajukan_at)
                                            <form action="{{ route('peminjam.pengembalian.batal', $item->id) }}" method="POST"
                                                  onsubmit="return confirm('Batalkan pengajuan pengembalian ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Hapus (Batalkan)</button>
                                            </form>
                                        @elseif($item->status === 'dipinjam')
                                            <form action="{{ route('peminjam.pengembalian.ajukan', $item->id) }}" method="POST"
                                                  onsubmit="return confirm('Ajukan pengembalian untuk alat ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">Ajukan Pengembalian</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Tidak ada aksi</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
