<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Menampilkan detail dan form edit profil akun yang sedang login.
    public function edit()
    {
        $user = auth()->user();

        // Jika admin atau petugas, arahkan ke view admin (yang pakai sidebar)
        if (in_array($user->role, ['admin', 'petugas'])) {
            return view('admin.profile.edit', compact('user'));
        }

        // Jika peminjam, arahkan ke view peminjam (yang pakai navbar atas ala katalog)
        return view('peminjam.profile.edit', compact('user'));
    }

    // Memperbarui hanya profil milik akun yang sedang login.
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_hp' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'foto_profile.image' => 'File yang diunggah harus berupa gambar.',
            'foto_profile.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto_profile.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto_profile')) {
            $folder = public_path('storage/profile');

            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $file = $request->file('foto_profile');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $filename);

            // Hapus foto lama setelah foto baru berhasil disimpan.
            if ($user->foto_profile) {
                $oldPhoto = public_path($user->foto_profile);
                if (is_file($oldPhoto)) {
                    @unlink($oldPhoto);
                }
            }

            $data['foto_profile'] = 'storage/profile/' . $filename;
        }

        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}