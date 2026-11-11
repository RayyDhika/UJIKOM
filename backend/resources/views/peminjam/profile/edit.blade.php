<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya – Peminjam</title>
    <!-- Hanya menggunakan Bootstrap 5 (Sama persis seperti di Katalog) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .profile-header {
            background: #111827;
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 24px;
        }
        .profile-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }
    </style>
</head>
<body>

    <!-- Navbar Atas (Sama persis seperti di Katalog) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('peminjam.katalog') }}">Panel Peminjam</a>
            <div class="d-flex">
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-sm me-2">Profile Saya</a>
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-light btn-sm me-2">Riwayat Pinjam</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin logout?')">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="profile-card">
                    <!-- Bagian Header Profil -->
                    <div class="profile-header">
                        <div class="d-flex align-items-center gap-3">
                            @if($user->foto_profile)
                                <img src="{{ asset($user->foto_profile) }}" alt="Foto profil"
                                     class="rounded-circle border border-2 border-white object-fit-cover" width="70" height="70">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold fs-4" style="width: 70px; height: 70px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1 fw-bold text-white">{{ $user->name }}</h4>
                                <p class="mb-0 text-white-50 small text-capitalize">{{ $user->role }}</p>
                                <p class="mb-0 text-white-50 small">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Edit Profil -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4">
                        @csrf
                        @method('PUT')

                        <h5 class="fw-bold text-dark mb-3">Informasi Akun</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small text-secondary">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="form-control @error('name') is-invalid @enderror">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-secondary">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="form-control @error('email') is-invalid @enderror">
                        </div>

                        <div class="mb-3">

                        <div class="mb-3">
                            <label for="no_hp" class="form-label fw-semibold small text-secondary">Nomor HP</label>
                            <input id="no_hp" type="text" name="no_hp" inputmode="numeric"
                                value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan nomor HP"
                                class="form-control @error('no_hp') is-invalid @enderror">
                        </div>

                        <div class="mb-4">
                            <label for="foto_profile" class="form-label fw-semibold small text-secondary">Foto Profil</label>
                            <input id="foto_profile" type="file" name="foto_profile" accept=".jpg,.jpeg,.png"
                                class="form-control @error('foto_profile') is-invalid @enderror">
                            <div class="form-text small">Format JPG, JPEG, PNG. Maksimal 2 MB. Kosongkan jika tidak ingin mengganti.</div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-dark mb-1">Ubah Password</h5>
                        <p class="text-muted small mb-3">Kosongkan kedua kolom jika tidak ingin mengubah password.</p>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-secondary">Password Baru</label>
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                class="form-control @error('password') is-invalid @enderror">
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                                class="form-control">
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('peminjam.katalog') }}" class="btn btn-light border px-4">Kembali</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>