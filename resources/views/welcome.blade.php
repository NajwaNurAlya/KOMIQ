<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOMIQ - Platform Membaca & Publikasi Komik Berbasis Web</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-brand { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col selection:bg-red-600 selection:text-white">

    <!-- Navbar -->
    <nav class="bg-zinc-900/90 backdrop-blur border-b border-zinc-800 sticky top-0 z-50 px-4 lg:px-8 py-3.5">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="w-9 h-9 rounded-xl bg-red-600 text-white font-brand text-xl flex items-center justify-center shadow-lg shadow-red-600/40 group-hover:scale-105 transition">K</span>
                <span class="text-2xl font-extrabold font-brand tracking-wider text-white">KOMIQ<span class="text-red-500">.</span></span>
            </a>

            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.chapter-reviews.index') }}" class="hidden sm:inline-flex px-3.5 py-2 rounded-xl text-xs font-semibold bg-red-950 border border-red-800 text-red-300 hover:bg-red-900/50 transition">
                            🛡️ Panel Admin
                        </a>
                    @endif
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-950/50 transition flex items-center gap-2">
                        <span>👤</span> {{ auth()->user()->name }}
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-300 hover:text-white hover:bg-zinc-800/80 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-950/50 transition">
                        Daftar Akun
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative overflow-hidden py-16 lg:py-24 border-b border-zinc-800/80 bg-gradient-to-b from-red-950/20 via-zinc-950 to-zinc-950">
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10 space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-950/80 border border-red-800 text-red-300 text-xs font-bold uppercase tracking-wider shadow">
                    <span>⚡</span> Platform Komik Masa Depan
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-brand tracking-tight text-white leading-tight">
                    Baca & Publikasikan Karya <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-rose-500 to-red-400">Komik Terbaik</span> di KOMIQ.
                </h1>

                <p class="text-zinc-400 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                    Menghubungkan jutaan pembaca dengan kreator komik digital. Nikmati berbagai genre populer, simulasi koin chapter premium, dan pengalaman membaca yang responsif.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                    @auth
                        <a href="{{ route('profile.show') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold text-sm shadow-xl shadow-red-900/40 hover:shadow-red-800/60 transition active:scale-95">
                            Buka Profil Saya &rarr;
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.chapter-reviews.index') }}" class="px-6 py-3.5 rounded-2xl bg-zinc-900 border border-zinc-700 hover:border-red-600 text-white font-bold text-sm transition">
                                Moderasi Chapter Admin
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold text-sm shadow-xl shadow-red-900/40 hover:shadow-red-800/60 transition active:scale-95">
                            Mulai Membaca Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-2xl bg-zinc-900 border border-zinc-700 hover:border-red-600 text-white font-bold text-sm transition">
                            Masuk ke Akun
                        </a>
                    @endauth
                </div>
            </div>
        </section>              

    <!-- Footer -->
    <footer class="border-t border-zinc-800 py-8 bg-zinc-900/50 text-center text-xs text-zinc-400">
        <p class="font-bold text-zinc-300">KOMIQ &copy; {{ date('Y') }} - Platform Membaca dan Publikasi Komik Berbasis Web</p>
        <p class="mt-1 text-zinc-500">Tugas Besar Pemrograman Web Lanjut - Program Studi Sistem Informasi, Universitas Lampung</p>
    </footer>

</body>
</html>
