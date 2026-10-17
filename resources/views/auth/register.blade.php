<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - KOMIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-brand { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-[#121318] text-zinc-100 min-h-screen flex flex-col justify-between selection:bg-red-600 selection:text-white relative">

    <!-- Top Navigation Bar (Sesuai Mockup SKPL UI01) -->
    <header class="bg-[#181920]/90 backdrop-blur border-b border-zinc-800/80 sticky top-0 z-50 px-6 py-3.5">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            
            <!-- Left: Logo & Links -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl font-black font-brand tracking-wider text-white">KOMIQ<span class="text-red-500">.</span></span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-zinc-400">
                    <a href="{{ route('home') }}" class="text-white hover:text-red-400 transition">Beranda</a>
                    <a href="{{ route('home') }}" class="hover:text-red-400 transition">Menjelajah</a>
                    <a href="{{ route('home') }}" class="hover:text-red-400 transition">Pusat Kreator</a>
                </nav>
            </div>

            <!-- Center: Search Bar Mockup -->
            <div class="hidden sm:flex flex-1 max-w-xs mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="Cari..." disabled
                        class="w-full bg-[#121318] border border-zinc-800 rounded-full px-4 py-1.5 text-xs text-zinc-400 placeholder-zinc-600 focus:outline-none cursor-default">
                    <span class="absolute right-3 top-2 text-zinc-600 text-xs">🔍</span>
                </div>
            </div>

            <!-- Right: Auth Action -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                    class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">
                    Masuk
                </a>
            </div>

        </div>
    </header>

    <!-- Main Content Area: Centered Sign Up Dialog Modal -->
    <main class="flex-1 flex items-center justify-center p-4 py-10">
        <div class="w-full max-w-md bg-[#1a1b23] border border-zinc-800 rounded-2xl shadow-2xl p-8 relative">
            
            <!-- Modal Header (SKPL-UI01: "Sign Up - Sudah punya akun? Sign In.") -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-extrabold text-white font-brand">Sign Up</h1>
                <p class="text-xs text-zinc-400 mt-1.5">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-red-500 hover:text-red-400 font-bold ml-1 underline underline-offset-2">
                        Sign In.
                    </a>
                </p>
            </div>

            <!-- Form Sign Up -->
            <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- Nama Lengkap / Username -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1">Nama Lengkap (Username)</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Nama Anda"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border @error('name') border-red-500 @else border-zinc-700/80 @enderror rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    @error('name')
                        <p class="mt-1 text-[11px] text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="email@email.com"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border @error('email') border-red-500 @else border-zinc-700/80 @enderror rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    @error('email')
                        <p class="mt-1 text-[11px] text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Peran Akun (Role) -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Daftar Sebagai Peran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center p-2 rounded-lg bg-[#121318] border border-zinc-800 hover:border-red-800 cursor-pointer transition">
                            <input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-600 bg-zinc-900 border-zinc-700">
                            <span class="ml-2 text-xs font-medium text-white">📖 Pembaca</span>
                        </label>
                        <label class="flex items-center p-2 rounded-lg bg-[#121318] border border-zinc-800 hover:border-red-800 cursor-pointer transition">
                            <input type="radio" name="role" value="kreator" {{ old('role') === 'kreator' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-600 bg-zinc-900 border-zinc-700">
                            <span class="ml-2 text-xs font-medium text-white">🎨 Kreator</span>
                        </label>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        placeholder="Minimal 8 karakter"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border @error('password') border-red-500 @else border-zinc-700/80 @enderror rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-zinc-300 mb-1">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                </div>

                <!-- Tombol Sign Up (Merah Solid Sesuai Mockup SKPL) -->
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs shadow-lg shadow-red-900/40 active:scale-[0.99] transition duration-200 uppercase tracking-wider mt-2">
                    Sign Up
                </button>
            </form>

            <!-- Disclaimer Text (Sesuai Catatan Bawah Mockup UI01) -->
            <p class="mt-5 text-[10px] text-zinc-400 text-center leading-relaxed">
                Dengan berlanjut, anda setuju dengan <a href="#" class="text-zinc-300 underline hover:text-white">Syarat Pengguna</a> dan <a href="#" class="text-zinc-300 underline hover:text-white">Privasi</a> platform KOMIQ.
            </p>

        </div>
    </main>

    <!-- Bottom Red Accent Stripe (Sesuai Garis Merah di Bagian Bawah Gambar SKPL-UI01) -->
    <div class="w-full h-3 bg-red-600 shadow-[0_-2px_15px_rgba(220,38,38,0.5)]"></div>

</body>
</html>