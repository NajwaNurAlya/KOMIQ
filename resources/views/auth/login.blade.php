<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - KOMIQ</title>
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

    <!-- Top Navigation Bar (Sesuai Mockup SKPL UI02) -->
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
                <a href="{{ route('register') }}"
                    class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition">
                    Daftar
                </a>
            </div>

        </div>
    </header>

    <!-- Main Content Area: Centered Sign In Dialog Modal -->
    <main class="flex-1 flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-md bg-[#1a1b23] border border-zinc-800 rounded-2xl shadow-2xl p-8 relative">
            
            <!-- Modal Header (SKPL-UI02: "Sign In - Tidak punya akun? Sign up.") -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-extrabold text-white font-brand">Sign In</h1>
                <p class="text-xs text-zinc-400 mt-1.5">
                    Tidak punya akun? 
                    <a href="{{ route('register') }}" class="text-red-500 hover:text-red-400 font-bold ml-1 underline underline-offset-2">
                        Sign up.
                    </a>
                </p>
            </div>

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-950/70 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-red-950/70 border border-red-800 text-red-300 text-xs flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Form Sign In -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="email@email.com"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border @error('email') border-red-500 @else border-zinc-700/80 @enderror rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    @error('email')
                        <p class="mt-1 text-[11px] text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-xs font-semibold text-zinc-300">Password</label>
                        <span class="text-[11px] text-zinc-500 hover:text-red-400 cursor-pointer transition">Lupa Password?</span>
                    </div>
                    <input type="password" id="password" name="password" required
                        placeholder="password"
                        class="w-full px-3.5 py-2.5 bg-[#121318] border @error('password') border-red-500 @else border-zinc-700/80 @enderror rounded-lg text-white text-xs placeholder-zinc-500 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    @error('password')
                        <p class="mt-1 text-[11px] text-red-400 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center text-xs text-zinc-400 py-0.5">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded bg-[#121318] border-zinc-700 text-red-600 focus:ring-red-600 focus:ring-offset-[#1a1b23]">
                        <span class="ml-2 text-[11px] text-zinc-400">Ingat saya</span>
                    </label>
                </div>

                <!-- Tombol Sign In (Merah Solid Sesuai Mockup SKPL) -->
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs shadow-lg shadow-red-900/40 active:scale-[0.99] transition duration-200 uppercase tracking-wider">
                    Sign In
                </button>
            </form>

            <!-- Social / Quick Test Login (Sesuai "atau sign in dengan...") -->
            <div class="mt-6 pt-5 border-t border-zinc-800/80 text-center">
                <span class="text-[11px] text-zinc-400 block mb-2 font-medium">Akun Pengujian Demo (Klik Cepat):</span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="quickFill('admin@komiq.com', 'password123')"
                        class="px-2 py-1.5 bg-[#121318] hover:bg-red-950/60 border border-zinc-800 hover:border-red-800 rounded-md text-[11px] text-red-400 font-semibold transition">
                        🛡️ Admin
                    </button>
                    <button type="button" onclick="quickFill('kreator@komiq.com', 'password123')"
                        class="px-2 py-1.5 bg-[#121318] hover:bg-amber-950/60 border border-zinc-800 hover:border-amber-800 rounded-md text-[11px] text-amber-400 font-semibold transition">
                        🎨 Kreator
                    </button>
                    <button type="button" onclick="quickFill('user@komiq.com', 'password123')"
                        class="px-2 py-1.5 bg-[#121318] hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 rounded-md text-[11px] text-zinc-300 font-semibold transition">
                        📖 User
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Red Accent Stripe (Sesuai Garis Merah di Bagian Bawah Gambar SKPL-UI01 & UI02) -->
    <div class="w-full h-3 bg-red-600 shadow-[0_-2px_15px_rgba(220,38,38,0.5)]"></div>

    <script>
        function quickFill(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>