@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Detail Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto">
    @if(session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Periksa kembali data yang kamu masukkan:</p>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="bg-gray-900 px-6 py-5 text-white">
            <div class="flex items-center gap-4">
                @if($user->foto_profile)
                    <img src="{{ asset($user->foto_profile) }}" alt="Foto profil"
                         class="h-20 w-20 rounded-full border-2 border-white object-cover">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-700 text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-300">{{ ucfirst($user->role) }}</p>
                    <p class="text-sm text-gray-300">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi Akun</h3>

            <div class="mb-4">
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="role" class="mb-2 block text-sm font-semibold text-gray-700">Role / Hak Akses</label>
                <input id="role" type="text" value="{{ ucfirst($user->role) }}" disabled
                    class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-500">
                <p class="mt-1 text-xs text-gray-500">Role akun hanya dapat dikelola oleh administrator.</p>
            </div>

            <div class="mb-4">
                <label for="no_hp" class="mb-2 block text-sm font-semibold text-gray-700">Nomor HP</label>
                <input id="no_hp" type="text" name="no_hp" inputmode="numeric"
                    value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan nomor HP"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('no_hp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="foto_profile" class="mb-2 block text-sm font-semibold text-gray-700">Foto Profil</label>
                <input id="foto_profile" type="file" name="foto_profile" accept=".jpg,.jpeg,.png"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-gray-500">Format JPG, JPEG, PNG. Maksimal 2 MB. Kosongkan jika tidak ingin mengganti foto.</p>
                @error('foto_profile') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4 border-t border-gray-200 pt-5">
                <h3 class="mb-1 text-lg font-semibold text-gray-800">Ubah Password</h3>
                <p class="mb-4 text-xs text-gray-500">Kosongkan kedua kolom jika tidak ingin mengubah password.</p>

                <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password Baru</label>
                <input id="password" type="password" name="password" autocomplete="new-password"
                    class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('password') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror

                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-gray-200 pt-5">
                <a href="{{ url()->previous() }}"
                    class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-300">Kembali</a>
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
