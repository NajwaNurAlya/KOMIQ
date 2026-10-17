<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KOMIQ Platform Komik</title>
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

    <div class="w-full max-w-4xl bg-zinc-900/90 backdrop-blur border border-zinc-800 rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        
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
                    Platform Baca & Publikasi Komik
                </div>
                
                <h1 class="text-3xl font-extrabold font-brand text-white leading-tight mb-3">
                    Jelajahi Dunia Komik & Webtoon Favoritmu.
                </h1>
                <p class="text-zinc-400 text-sm leading-relaxed">
                    Masuk ke akunmu untuk membaca chapter pilihan, mendukung kreator berbakat, dan mengelola koleksi koinmu.
                </p>
            </div>

            <!-- Demo Credentials Helper -->
            <div class="relative z-10 mt-8 pt-6 border-t border-zinc-800/80">
                <p class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Akun Demo Pengujian:</p>
                <div class="space-y-1.5 text-xs text-zinc-300">
                    <div class="flex justify-between items-center bg-zinc-950/60 px-3 py-1.5 rounded-lg border border-zinc-800">
                        <span class="font-medium text-red-400">Admin:</span>
                        <code class="text-zinc-400">admin@komiq.com</code>
                    </div>
                    <div class="flex justify-between items-center bg-zinc-950/60 px-3 py-1.5 rounded-lg border border-zinc-800">
                        <span class="font-medium text-amber-400">Kreator:</span>
                        <code class="text-zinc-400">kreator@komiq.com</code>
                    </div>
                    <div class="flex justify-between items-center bg-zinc-950/60 px-3 py-1.5 rounded-lg border border-zinc-800">
                        <span class="font-medium text-zinc-300">User:</span>
                        <code class="text-zinc-400">user@komiq.com</code>
                    </div>
                    <p class="text-[11px] text-zinc-500 text-center mt-1">Password semua akun demo: <span class="text-red-400 font-mono">password123</span></p>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="lg:col-span-7 p-8 lg:p-10 bg-zinc-900 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold font-brand text-white">Selamat Datang Kembali!</h2>
                <p class="text-sm text-zinc-400 mt-1">Silakan masukkan email dan kata sandi akun KOMIQ Anda.</p>
            </div>

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-sm flex items-center gap-3">
                    <span class="text-lg">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-950/60 border border-red-800/80 text-red-300 text-sm flex items-center gap-3">
                    <span class="text-lg">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-zinc-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com"
                            class="w-full px-4 py-3 bg-zinc-950 border @error('email') border-red-500 @else border-zinc-800 @enderror rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-zinc-300">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-zinc-950 border @error('password') border-red-500 @else border-zinc-800 @enderror rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/30 transition text-sm">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-sm py-1">
                    <label class="flex items-center text-zinc-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-zinc-950 border-zinc-800 text-red-600 focus:ring-red-600 focus:ring-offset-zinc-900">
                        <span class="ml-2 text-xs text-zinc-300 font-medium">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-900/50 hover:shadow-red-800/60 active:scale-[0.99] transition duration-200 text-sm">
                    Masuk ke Akun
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-zinc-800 text-center text-sm text-zinc-400">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="text-red-500 hover:text-red-400 font-bold ml-1 transition underline underline-offset-4">
                    Daftar Sekarang
                </a>
            </div>
        </div>

    </div>

</body>
</html>