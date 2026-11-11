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

    <!-- Card Container (Lebar diubah menjadi w-96 agar sama persis dan ramping seperti form login) -->
    <div class="bg-white p-6 rounded-lg shadow-md w-96 mx-4">
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-4">Registrasi Sistem</h3>

        <!-- Alert Error Session -->
        @if(session('success'))
            <div class="mb-3 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg shadow-sm text-sm">
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
                <label class="block text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            <!-- No Telepon -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-1">No Telepon</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" inputmode="numeric"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('no_hp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200 text-sm">
                Register
            </button>
            
            <!-- Tautan Kembali ke Login -->
            <div class="mt-4 text-center text-sm text-gray-600">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login sekarang</a>
            </div>
        </form>
    </div>

</body>
</html>