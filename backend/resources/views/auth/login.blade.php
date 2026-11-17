<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Alat</title>
    <!-- Memuat Tailwind CSS melalui CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <!-- Container Utama (Card Dua Kolom) -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row w-full max-w-4xl mx-4">
        
        <!-- Sisi Kiri (Panel Merah) -->
        <div class="bg-red-600 text-white p-10 md:w-1/2 flex flex-col justify-center rounded-r-[80px] md:rounded-r-[120px] relative">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Selamat Datang!</h2>
            <p class="text-red-100 text-sm md:text-base leading-relaxed">
                Platform Sistem Peminjaman Alat Sekolah yang Cepat, Aman, dan Terpadu.
            </p>
        </div>

        <!-- Sisi Kanan (Form Login) -->
        <div class="p-8 md:p-12 md:w-1/2 flex flex-col justify-center">
            <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">Masuk</h3>

            <!-- Alert Error Session -->
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Alert Error Validasi -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    <ul class="list-disc pl-5 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Input Email -->
                <div class="mb-4">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email / Username" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                </div>

                <!-- Input Password -->
                <div class="mb-6">
                    <input type="password" name="password" placeholder="Kata sandi" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                    class="w-full bg-red-600 text-white font-semibold py-3 rounded-xl hover:bg-red-700 transition duration-200 text-sm shadow-md">
                    Masuk
                </button>
            </form>

            <!-- Tautan ke Register -->
            <div class="mt-6 text-center text-sm text-gray-600">
                Belum punya akun? 
                <a href="{{ route('auth.registrasi') }}" class="text-red-600 font-medium hover:underline">Daftar sekarang</a>
            </div>
        </div>

    </div>

</body>
</html>