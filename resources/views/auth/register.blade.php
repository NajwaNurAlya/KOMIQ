<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - KOMIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-brand { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center p-4 selection:bg-red-600 selection:text-white">

    <div class="w-full max-w-4xl bg-zinc-900/90 backdrop-blur border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 my-6">
        
        <!-- Sisi Kiri: Visual Branding Merah & Hitam -->
        <div class="lg:col-span-5 bg-gradient-to-br from-red-950 via-zinc-900 to-black p-8 lg:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-zinc-800 relative overflow-hidden">
            <!-- Glow background effect -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-red-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-red-800/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-8 group">
                    <span class="w-10 h-10 rounded-xl bg-red-600 text-white font-brand text-2xl flex items-center justify-center shadow-lg shadow-red-600/40 group-hover:scale-105 transition">K</span>
                    <span class="text-3xl font-extrabold font-brand tracking-wider text-white">KOMIQ<span class="text-red-500">.</span></span>
                </a>
                
                <div class="inline-block px-3 py-1 rounded-full bg-red-900/40 border border-red-700/50 text-red-300 text-xs font-semibold uppercase tracking-wider mb-4">
                    Gabung Komunitas Kami
                </div>
                
                <h1 class="text-3xl font-extrabold font-brand text-white leading-tight mb-3">
                    Mulai Petualangan Komik Tanpa Batas.
                </h1>
                <p class="text-zinc-400 text-sm leading-relaxed">
                    Daftar akun gratis sekarang untuk menikmati ratusan judul komik, berdiskusi di kolom komentar, atau mulai berkarya sebagai Kreator.
                </p>
            </div>

            <div class="relative z-10 mt-8 pt-6 border-t border-zinc-800/80">
                <div class="space-y-3">
                    <div class="flex items-start gap-3 text-xs text-zinc-300">
                        <span class="text-red-400 text-base">✨</span>
                        <div>
                            <strong class="text-white block font-medium">Sebagai Pembaca:</strong>
                            Akses chapter gratis dan premium, simpan histori bacaan, dan dukung karya favorit.
                        </div>
                    </div>
                    <div class="flex items-start gap-3 text-xs text-zinc-300">
                        <span class="text-red-400 text-base">🎨</span>
                        <div>
                            <strong class="text-white block font-medium">Sebagai Kreator:</strong>
                            Publikasikan karya komikmu, dapatkan pembaca setia, dan raih koin dari chapter premium.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Form Register -->
        <div class="lg:col-span-7 p-8 lg:p-10 bg-zinc-900 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold font-brand text-white">Buat Akun Baru</h2>
                <p class="text-sm text-zinc-400 mt-1">Isi formulir di bawah ini dengan data yang valid.</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-zinc-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Contoh: Irma Nurhayati"
                        class="w-full px-4 py-3 bg-zinc-950 border @error('name') border-red-500 @else border-zinc-800 @enderror rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-zinc-300 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 bg-zinc-950 border @error('email') border-red-500 @else border-zinc-800 @enderror rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Pilihan Role (Peran) -->
                <div>
                    <label class="block text-sm font-semibold text-zinc-300 mb-1.5">Daftar Sebagai (Peran Akun)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 rounded-xl bg-zinc-950 border border-zinc-800 hover:border-red-800/80 cursor-pointer transition">
                            <input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-600 bg-zinc-900 border-zinc-700">
                            <div class="ml-2.5">
                                <span class="block text-xs font-bold text-white">📖 Pembaca</span>
                                <span class="block text-[11px] text-zinc-400">Menikmati konten</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 rounded-xl bg-zinc-950 border border-zinc-800 hover:border-red-800/80 cursor-pointer transition">
                            <input type="radio" name="role" value="kreator" {{ old('role') === 'kreator' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-600 bg-zinc-900 border-zinc-700">
                            <div class="ml-2.5">
                                <span class="block text-xs font-bold text-white">🎨 Kreator</span>
                                <span class="block text-[11px] text-zinc-400">Publikasi komik</span>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-zinc-300 mb-1.5">Kata Sandi</label>
                    <input type="password" id="password" name="password" required
                        placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-3 bg-zinc-950 border @error('password') border-red-500 @else border-zinc-800 @enderror rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-zinc-300 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Ulangi kata sandi Anda"
                        class="w-full px-4 py-3 bg-zinc-950 border border-zinc-800 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-900/50 hover:shadow-red-800/60 active:scale-[0.99] transition duration-200 text-sm mt-2">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-zinc-800 text-center text-sm text-zinc-400">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-red-500 hover:text-red-400 font-bold ml-1 transition underline underline-offset-4">
                    Masuk di Sini
                </a>
            </div>
        </div>

    </div>

</body>
</html>