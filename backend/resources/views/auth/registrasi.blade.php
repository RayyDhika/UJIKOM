<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Peminjaman Alat</title>
    <!-- Memuat Tailwind CSS melalui CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-6">

    <!-- Container Utama (Card Dua Kolom) -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row w-full max-w-4xl mx-4">
        
        <!-- Sisi Kiri (Panel Merah) -->
        <div class="bg-red-600 text-white p-10 md:w-1/2 flex flex-col justify-center rounded-r-[80px] md:rounded-r-[120px] relative">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Bergabung Bersama Kami!</h2>
            <p class="text-red-100 text-sm md:text-base leading-relaxed">
                Buat akun baru untuk mulai melakukan peminjaman alat secara online dengan mudah.
            </p>
        </div>

        <!-- Sisi Kanan (Form Register) -->
        <div class="p-8 md:p-10 md:w-1/2 flex flex-col justify-center">
            <h3 class="text-2xl font-bold text-center text-gray-800 mb-4">Registrasi Sistem</h3>

            <!-- Alert Session -->
            @if(session('success'))
                <div class="mb-3 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('auth.registrasi.store') }}" method="POST">
                @csrf

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message}}</span>@enderror
                </div>

                <!-- No Telepon -->
                <div class="mb-5">
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="No Telepon" inputmode="numeric"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    @error('no_hp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-3">
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                    class="w-full bg-red-600 text-white font-semibold py-3 rounded-xl hover:bg-red-700 transition duration-200 text-sm shadow-md">
                    Register
                </button>
                
                <!-- Tautan Kembali ke Login -->
                <div class="mt-4 text-center text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-red-600 font-medium hover:underline">Login sekarang</a>
                </div>
            </form>
        </div>

    </div>

</body>
</html>