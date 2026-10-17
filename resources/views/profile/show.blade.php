<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - {{ $user->name }} | KOMIQ</title>
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

    <!-- Top Navigation Bar -->
    <nav class="bg-zinc-900/90 backdrop-blur border-b border-zinc-800 sticky top-0 z-50 px-4 lg:px-8 py-3.5">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="w-9 h-9 rounded-xl bg-red-600 text-white font-brand text-xl flex items-center justify-center shadow-lg shadow-red-600/40 group-hover:scale-105 transition">K</span>
                    <span class="text-2xl font-extrabold font-brand tracking-wider text-white">KOMIQ<span class="text-red-500">.</span></span>
                </a>

                <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                        🏠 Beranda
                    </a>
                    @if($user->isAdmin())
                        <a href="{{ route('admin.chapter-reviews.index') }}" class="px-3 py-1.5 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-950/40 border border-red-900/50 transition flex items-center gap-1.5 font-semibold">
                            🛡️ Moderasi Chapter
                        </a>
                    @endif
                </div>
            </div>

            <!-- User Menu Right -->
            <div class="flex items-center gap-3">
                <!-- Coin Balance Pill -->
                <div class="hidden sm:flex items-center gap-2 bg-gradient-to-r from-red-950/80 to-zinc-900 border border-red-900/50 px-3.5 py-1.5 rounded-full text-xs font-bold text-amber-300 shadow-inner">
                    <span>🪙</span>
                    <span>{{ number_format($user->coin_balance) }} Koin</span>
                </div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-800 hover:bg-red-600 text-zinc-300 hover:text-white transition duration-200 shadow">
                        Keluar
                    </button>
                </form>
            </div>

        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-4 lg:p-8 space-y-6">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-950/70 border border-emerald-800/80 text-emerald-300 text-sm flex items-center gap-3 shadow-lg">
                <span class="text-xl">✅</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-950/70 border border-red-800/80 text-red-300 text-sm flex items-center gap-3 shadow-lg">
                <span class="text-xl">⚠️</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-red-950/70 border border-red-800/80 text-red-300 text-sm shadow-lg">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <span>⚠️</span> Terdapat kesalahan validasi:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-red-300/90 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Hero Banner Card -->
        <div class="bg-gradient-to-r from-red-950/60 via-zinc-900 to-zinc-900 border border-zinc-800 rounded-3xl p-6 lg:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
                
                <!-- Avatar & Upload Form -->
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-28 h-28 lg:w-32 lg:h-32 rounded-full object-cover border-4 border-red-600 shadow-xl shadow-red-950/50 group-hover:brightness-90 transition">
                        
                        <!-- Upload Avatar Modal / Trigger -->
                        <label for="avatar_input" title="Ganti Foto Profil"
                            class="absolute bottom-1 right-1 bg-red-600 hover:bg-red-500 text-white p-2 rounded-full cursor-pointer shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                    </div>

                    <!-- Hidden Form Auto Submit on file selection -->
                    <form id="avatar_form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" id="avatar_input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="document.getElementById('avatar_form').submit();">
                    </form>
                    <span class="text-[11px] text-zinc-400 mt-2 font-medium">Klik ikon kamera untuk ganti foto</span>
                </div>

                <!-- User Meta Information -->
                <div class="flex-1 text-center md:text-left space-y-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                        <h1 class="text-2xl lg:text-3xl font-extrabold font-brand text-white">{{ $user->name }}</h1>
                        
                        <!-- Role Badge -->
                        @if($user->isAdmin())
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-950 border border-red-700 text-red-300 flex items-center gap-1 shadow">
                                🛡️ Admin
                            </span>
                        @elseif($user->isKreator())
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-950 border border-amber-700 text-amber-300 flex items-center gap-1 shadow">
                                🎨 Kreator Komik
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-zinc-800 border border-zinc-700 text-zinc-300 flex items-center gap-1 shadow">
                                📖 Pembaca Komik
                            </span>
                        @endif

                        <!-- Active Status Badge -->
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-950/80 border border-emerald-800 text-emerald-300 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Aktif
                        </span>
                    </div>

                    <p class="text-sm text-zinc-400 font-mono">{{ $user->email }}</p>
                    
                    <div class="pt-2 flex flex-wrap justify-center md:justify-start gap-4 text-xs text-zinc-400">
                        <div>
                            <span class="text-zinc-500">Bergabung:</span>
                            <span class="font-medium text-zinc-300">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '17 Oktober 2026' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">ID Pengguna:</span>
                            <span class="font-mono text-zinc-300">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Coin Balance Card Widget -->
                <div class="w-full md:w-auto bg-zinc-950/80 border border-zinc-800 rounded-2xl p-4 lg:p-5 flex flex-col justify-between text-center md:text-right min-w-[210px] shadow-lg">
                    <span class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Saldo Koin Anda</span>
                    <div class="my-1.5 flex items-center justify-center md:justify-end gap-2 text-2xl lg:text-3xl font-extrabold text-amber-400 font-brand">
                        <span>🪙</span>
                        <span>{{ number_format($user->coin_balance) }}</span>
                    </div>
                    <span class="text-[11px] text-zinc-500">Digunakan membuka Chapter Premium</span>
                </div>

            </div>

        </div>

        <!-- Role Action Notice / Shortcut -->
        @if($user->isAdmin())
            <div class="bg-red-950/30 border border-red-800/50 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600/20 text-red-400 text-xl flex items-center justify-center font-bold">
                        🛡️
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm">Hak Akses Administrator Aktif</h3>
                        <p class="text-xs text-zinc-400">Anda memiliki izin untuk mengawasi dan menyetujui (approve) chapter yang diajukan kreator.</p>
                    </div>
                </div>
                <a href="{{ route('admin.chapter-reviews.index') }}"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-red-950/50 transition whitespace-nowrap">
                    Buka Panel Moderasi Chapter &rarr;
                </a>
            </div>
        @elseif($user->isKreator())
            <div class="bg-amber-950/30 border border-amber-800/50 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600/20 text-amber-400 text-xl flex items-center justify-center font-bold">
                        🎨
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm">Ruang Kreator KOMIQ</h3>
                        <p class="text-xs text-zinc-400">Publikasikan karya komik terbaik Anda dan kirim chapter baru untuk diperiksa Admin.</p>
                    </div>
                </div>
                <a href="{{ route('home') }}"
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-xl shadow-lg transition whitespace-nowrap">
                    Jelajahi Beranda Komik &rarr;
                </a>
            </div>
        @endif

        <!-- Grid 2 Kolom Formulir Pengaturan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Formulir 1: Edit Informasi Biodata -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 lg:p-7 shadow-xl">
                <div class="flex items-center gap-3 mb-5 border-b border-zinc-800 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-600/20 text-red-500 flex items-center justify-center font-bold text-sm">
                        👤
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white font-brand">Informasi Profil</h2>
                        <p class="text-xs text-zinc-400">Perbarui nama tampilan dan alamat email akun Anda.</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-white text-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-white text-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                        @error('email')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Peran Akun (Role)</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled
                            class="w-full px-4 py-2.5 bg-zinc-950/60 border border-zinc-800/60 rounded-xl text-zinc-400 text-sm cursor-not-allowed">
                        <span class="text-[11px] text-zinc-500 mt-1 block">Role diatur oleh sistem & administrator.</span>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-sm shadow-lg shadow-red-950/50 transition duration-200 mt-2">
                        Simpan Perubahan Profil
                    </button>
                </form>
            </div>

            <!-- Formulir 2: Ubah Kata Sandi -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 lg:p-7 shadow-xl">
                <div class="flex items-center gap-3 mb-5 border-b border-zinc-800 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-600/20 text-red-500 flex items-center justify-center font-bold text-sm">
                        🔒
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white font-brand">Keamanan & Kata Sandi</h2>
                        <p class="text-xs text-zinc-400">Pastikan akun Anda terlindungi dengan sandi yang kuat.</p>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-white text-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                        <input type="password" id="new_password" name="new_password" required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-white text-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                            placeholder="Ulangi kata sandi baru"
                            class="w-full px-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-xl text-white text-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-xl text-sm transition duration-200 mt-2">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-800 py-6 text-center text-xs text-zinc-500 mt-10">
        <p>&copy; {{ date('Y') }} KOMIQ Platform Komik Berbasis Web. Modul Profil & Autentikasi oleh Irma Nurhayati.</p>
    </footer>

</body>
</html>