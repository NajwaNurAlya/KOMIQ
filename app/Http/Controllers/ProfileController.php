<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil pengguna saat ini.
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Memperbarui informasi biodata (Nama & Email).
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    /**
     * Mengunggah atau memperbarui foto profil (avatar).
     */
    public function updateAvatar(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih file foto profil terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar yang diizinkan adalah jpeg, png, jpg, atau webp.',
            'avatar.max' => 'Ukuran file gambar maksimal 2 MB.',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke direktori storage/app/public/avatars
            $destinationPath = storage_path('app/public/avatars');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // Hapus foto profil lama jika ada di storage lokal
            if ($user->avatar && str_starts_with($user->avatar, 'storage/avatars/')) {
                $oldPath = public_path($user->avatar);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file->move($destinationPath, $filename);
            $user->avatar = 'storage/avatars/' . $filename;
            $user->save();
        }

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Memperbarui kata sandi pengguna.
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Kata sandi Anda berhasil diubah!');
    }
}