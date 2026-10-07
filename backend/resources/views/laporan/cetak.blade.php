<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        @page { margin: 24px; }
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1f2937; font-size: 11px; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 12px; }
        .filter-box { margin-bottom: 14px; font-size: 10px; color: #374151; }
        .filter-box span { display: inline-block; margin-right: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; text-transform: uppercase; font-size: 9px; letter-spacing: 0.03em; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .badge-diajukan { background: #fef9c3; color: #854d0e; }
        .badge-dipinjam { background: #dbeafe; color: #1e40af; }
        .badge-dikembalikan { background: #d1fae5; color: #065f46; }
        .badge-telat { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; }
        ul { margin: 0; padding-left: 14px; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Alat</h1>
    <div class="subtitle">Dicetak pada {{ now()->format('d M Y H:i') }} WIB</div>

    <div class="filter-box">
        <span><strong>Estimasi Waktu:</strong>
            {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d M Y') : 'Semua' }}
            &ndash;
            {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d M Y') : 'Semua' }}
        </span>
        <span><strong>Kondisi/Status:</strong> {{ $status ? ucfirst($status) : 'Semua Status' }}</span>
        <span><strong>Total Data:</strong> {{ $laporan->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Detail Alat</th>
                <th class="text-center">Status</th>
                <th>Kondisi Kembali</th>
                <th>Denda</th>
                <th>Petugas Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->user->name ?? 'User Dihapus' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td>
                    <td>{{ $item->tgl_kembali_plan ? \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') : '-' }}</td>
                    <td>
                        <ul>
                            @foreach($item->detailPinjam as $detail)
                                <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} (Jumlah: {{ $detail->jumlah }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td>{{ $item->pengembalian->kondisi_kembali ?? '-' }}</td>
                    <td>{{ isset($item->pengembalian) ? 'Rp ' . number_format($item->pengembalian->denda, 0, ',', '.') : '-' }}</td>
                    <td>{{ $item->pengembalian->petugas->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data laporan peminjaman berdasarkan filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Sistem Peminjaman Alat &mdash; Laporan ini dibuat otomatis oleh sistem.</div>
</body>
</html>
