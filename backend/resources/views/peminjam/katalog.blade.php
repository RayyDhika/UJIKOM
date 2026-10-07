<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat – Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }

        .alat-card {
            background: #fff;
            border: 2px solid transparent;
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
        }
        .alat-card:hover { box-shadow: 0 4px 14px rgba(0, 0, 0, .08); }
        .alat-card.selected { border-color: #0d6efd; }

        .alat-img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; background: #fff; }
        .alat-img-placeholder {
            width: 100%; aspect-ratio: 1 / 1;
            display: flex; align-items: center; justify-content: center;
            background: #eef1f5; color: #9aa4b2; font-size: .85rem;
        }

        .pilih-circle {
            position: absolute; top: 12px; right: 12px;
            width: 24px; height: 24px; border-radius: 50%;
            border: 1.5px solid #ced4da; background: #fff;
            display: flex; align-items: center; justify-content: center;
            color: transparent; font-size: 14px; line-height: 1;
        }
        .alat-card.selected .pilih-circle { background: #0d6efd; border-color: #0d6efd; color: #fff; }
        .pilih-input { position: absolute; opacity: 0; pointer-events: none; }

        .alat-body { padding: 16px; display: flex; flex-direction: column; flex: 1; }
        .alat-nama { font-size: .95rem; font-weight: 600; margin-bottom: 8px; }
        .alat-desk { font-size: .8rem; color: #8a94a3; margin-bottom: 8px; min-height: 2.4em; }
        .alat-meta { font-size: .78rem; color: #6c757d; margin-bottom: 14px; }
        .dot-baik { color: #198754; }
        .dot-lain { color: #dc3545; }

        .alat-footer {
            margin-top: auto; display: flex; align-items: center;
            justify-content: space-between; font-size: .8rem; color: #8a94a3;
        }

        .qty {
            display: inline-flex; align-items: center;
            border: 1px solid #dee2e6; border-radius: 999px;
            padding: 2px 6px; background: #fff;
        }
        .qty button {
            border: 0; background: transparent; width: 26px; height: 26px;
            font-size: 1rem; line-height: 1; color: #495057;
        }
        .qty input {
            width: 32px; border: 0; text-align: center; font-size: .85rem;
            background: transparent; -moz-appearance: textfield;
        }
        .qty input::-webkit-outer-spin-button,
        .qty input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .qty input:focus { outline: none; }

        .ajukan-bar {
            background: #fff; border-radius: 14px; padding: 14px 20px;
            display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
        }
        .ajukan-bar .label-kembali { font-weight: 600; font-size: .9rem; }
        .ajukan-bar .terpilih { color: #6c757d; font-size: .9rem; }
        .ajukan-bar .divider { width: 1px; height: 22px; background: #dee2e6; }
        .ajukan-bar .btn-ajukan { margin-left: auto; border-radius: 999px; padding: 8px 28px; font-weight: 600; }

        .search-wrap { position: relative; min-width: 280px; }
        .search-wrap input { border-radius: 999px; padding-left: 40px; }
        .search-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9aa4b2; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Panel Peminjam</a>
            <div class="d-flex">
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-light btn-sm me-2">Riwayat Pinjam</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1">Katalog Alat</h3>
                <p class="text-muted mb-0">Pilih alat yang ingin kamu pinjam, lalu ajukan.</p>
            </div>
            <div class="search-wrap">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
                <input type="text" id="cari" class="form-control" placeholder="Cari nama alat...">
            </div>
        </div>

        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" id="form-ajukan">
            @csrf

            {{-- Rencana kembali + tombol ajukan di ATAS katalog --}}
            <div class="ajukan-bar shadow-sm mb-4">
                <span class="terpilih"><span id="jumlah-terpilih">0</span> alat dipilih</span>
                <span class="divider"></span>
                <label for="tgl_kembali_plan" class="label-kembali mb-0">Rencana Kembali</label>
                <input type="date" id="tgl_kembali_plan" name="tgl_kembali_plan"
                       class="form-control" style="max-width: 200px;"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       value="{{ old('tgl_kembali_plan') }}" required>
                <button type="submit" class="btn btn-primary btn-ajukan">Ajukan Peminjaman</button>
            </div>

            <div class="row g-4" id="grid-alat">
                @forelse($alats as $alat)
                    <div class="col-12 col-sm-6 col-lg-3 item-alat" data-nama="{{ strtolower($alat->nama_alat) }}">
                        <div class="alat-card" data-stok="{{ $alat->stok }}">
                            <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="pilih-input">
                            <span class="pilih-circle">&#10003;</span>

                            {{-- Gambar dari data alat admin (disimpan sebagai storage/alat/xxx) --}}
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="alat-img">
                            @else
                                <div class="alat-img-placeholder">Tidak ada foto</div>
                            @endif

                            <div class="alat-body">
                                <div class="alat-nama">{{ $alat->nama_alat }}</div>
                                <div class="alat-desk">{{ \Illuminate\Support\Str::limit($alat->deskripsi ?? '', 70) }}</div>
                                <div class="alat-meta">
                                    {{ $alat->kategori->nama_kategori ?? '-' }}
                                    &nbsp;·&nbsp;
                                    <span class="{{ strtolower($alat->status_kondisi) === 'baik' ? 'dot-baik' : 'dot-lain' }}">●</span>
                                    {{ $alat->status_kondisi }}
                                </div>

                                <div class="alat-footer">
                                    <span>Stok {{ $alat->stok }}</span>
                                    {{-- Input jumlah baru dikirim kalau kartu dipilih (disabled = tidak terkirim),
                                         jadi urutan jumlah[] selalu sama dengan alat_id[] --}}
                                    <div class="qty">
                                        <button type="button" class="btn-minus">&minus;</button>
                                        <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}" disabled>
                                        <button type="button" class="btn-plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center">Tidak ada alat yang tersedia saat ini.</div>
                    </div>
                @endforelse
            </div>

            <div id="kosong-cari" class="text-center text-muted py-5 d-none">Alat tidak ditemukan.</div>
        </form>
    </div>

    <script>
        const penghitung = document.getElementById('jumlah-terpilih');

        function hitungTerpilih() {
            penghitung.textContent = document.querySelectorAll('.pilih-input:checked').length;
        }

        document.querySelectorAll('.alat-card').forEach(card => {
            const cek  = card.querySelector('.pilih-input');
            const qty  = card.querySelector('.qty input');
            const stok = parseInt(card.dataset.stok, 10);

            function setPilih(nilai) {
                cek.checked = nilai;
                qty.disabled = !nilai;
                card.classList.toggle('selected', nilai);
                hitungTerpilih();
            }

            card.addEventListener('click', e => {
                if (e.target.closest('.qty')) return;
                setPilih(!cek.checked);
            });

            card.querySelector('.btn-minus').addEventListener('click', () => {
                if (!cek.checked) setPilih(true);
                qty.value = Math.max(1, (parseInt(qty.value, 10) || 1) - 1);
            });
            card.querySelector('.btn-plus').addEventListener('click', () => {
                if (!cek.checked) setPilih(true);
                qty.value = Math.min(stok, (parseInt(qty.value, 10) || 1) + 1);
            });
            qty.addEventListener('change', () => {
                qty.value = Math.min(stok, Math.max(1, parseInt(qty.value, 10) || 1));
            });
        });

        document.getElementById('cari').addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let ada = 0;
            document.querySelectorAll('.item-alat').forEach(el => {
                const cocok = el.dataset.nama.includes(q);
                el.classList.toggle('d-none', !cocok);
                if (cocok) ada++;
            });
            document.getElementById('kosong-cari').classList.toggle('d-none', ada > 0);
        });

        document.getElementById('form-ajukan').addEventListener('submit', e => {
            if (!document.querySelectorAll('.pilih-input:checked').length) {
                e.preventDefault();
                alert('Pilih minimal satu alat terlebih dahulu.');
            }
        });
    </script>
</body>
</html>
