<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Pengguna - KOMIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-brand { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-[#121318] text-zinc-100 min-h-screen flex flex-col selection:bg-red-600 selection:text-white">

    <!-- Top Navigation Bar (Sesuai Mockup SKPL UI05 Header) -->
    <header class="bg-[#181920]/95 backdrop-blur border-b border-zinc-800/80 sticky top-0 z-50 px-6 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            
            <!-- Left: Logo & Links -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl font-black font-brand tracking-wider text-white">KOMIQ<span class="text-red-500">.</span></span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-zinc-400">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                    <a href="{{ route('home') }}" class="hover:text-white transition">Menjelajah</a>
                    @if($user->isAdmin() || $user->isKreator())
                        <a href="{{ $user->isAdmin() ? route('admin.chapter-reviews.index') : route('home') }}" class="text-red-400 hover:text-red-300 font-bold transition">
                            Pusat Kreator & Moderasi
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Center: Search Pill -->
            <div class="hidden sm:flex flex-1 max-w-xs mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="Cari..." disabled
                        class="w-full bg-[#121318] border border-zinc-800 rounded-full px-4 py-1.5 text-xs text-zinc-400 placeholder-zinc-600 focus:outline-none cursor-default">
                    <span class="absolute right-3 top-2 text-zinc-600 text-xs">🔍</span>
                </div>
            </div>

            <!-- Right: User Avatar & Coin Balance -->
            <div class="flex items-center gap-4">
                <!-- Saldo Koin (Ikon Uang) -->
                <div class="flex items-center gap-2 bg-[#121318] border border-zinc-800 px-3 py-1 rounded-full text-xs font-bold text-amber-300 shadow-inner">
                    <span>💵</span>
                    <span>{{ number_format($user->coin_balance) }} Koin</span>
                </div>

                <!-- User Profile Pill -->
                <div class="flex items-center gap-2.5">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-red-600">
                    <span class="hidden md:inline text-xs font-bold text-white">{{ $user->name }}</span>
                </div>

                <!-- Logout (Tombol Tulisan Keluar Sesuai Gambar 3) -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-zinc-800 hover:bg-red-600 text-zinc-200 hover:text-white text-xs font-bold transition duration-200">
                        Keluar
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- Main Container: Pengaturan Pengguna (Sesuai Layout SKPL-UI05) -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-4 lg:p-8 space-y-6">

        <!-- Title Header -->
        <div class="border-b border-zinc-800 pb-4">
            <h1 class="text-2xl font-bold font-brand text-white">Pengaturan Pengguna</h1>
            <p class="text-xs text-zinc-400 mt-1">Kelola data profil pribadi, keamanan kata sandi, dan preferensi akun Anda.</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-950/70 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2 shadow-lg">
                <span>✅</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-xl bg-red-950/70 border border-red-800 text-red-300 text-xs flex items-center gap-2 shadow-lg">
                <span>⚠️</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-red-950/70 border border-red-800 text-red-300 text-xs shadow-lg space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <span>⚠️</span> Terdapat kesalahan input:
                </div>
                <ul class="list-disc list-inside text-[11px] pl-2 text-red-300/90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Container Dua Kolom: Sidebar Menu (Kiri) + Konten Pengaturan (Kanan) -->
        <div class="bg-[#181920] border border-zinc-800 rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[550px]">
            
            <!-- Sidebar Kiri: Menu Kategori Pengaturan (SKPL UI05) -->
            <aside class="md:col-span-4 lg:col-span-3 bg-[#14151b] border-r border-zinc-800/80 p-4 space-y-1">
                <div class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">
                    Menu Akun
                </div>

                <!-- Tab 1: Informasi Akun -->
                <button type="button" onclick="switchTab('tab-profile')" id="btn-tab-profile"
                    class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-red-600/15 border border-red-800/60 text-red-400 transition">
                    <span class="text-sm">👤</span>
                    <span>Informasi Akun</span>
                </button>

                <!-- Tab 2: Keamanan Sandi -->
                <button type="button" onclick="switchTab('tab-security')" id="btn-tab-security"
                    class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                    <span class="text-sm">🔒</span>
                    <span>Keamanan & Sandi</span>
                </button>

                <!-- Tab 3: Saldo & Koin (Ikon Uang) -->
                <button type="button" onclick="switchTab('tab-coins')" id="btn-tab-coins"
                    class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                    <span class="text-sm">💵</span>
                    <span>Saldo & Koin</span>
                </button>

                <!-- Tab 4: Tampilan & Tema (Page 37 Mockup) -->
                <button type="button" onclick="switchTab('tab-appearance')" id="btn-tab-appearance"
                    class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                    <span class="text-sm">👁️</span>
                    <span>Pengaturan Tampilan</span>
                </button>

                <!-- Tab 5: Bahasa -->
                <button type="button" onclick="switchTab('tab-language')" id="btn-tab-language"
                    class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                    <span class="text-sm">🌐</span>
                    <span>Bahasa</span>
                </button>
            </aside>

            <!-- Panel Kanan: Konten Tab Sesuai Kategori -->
            <section class="md:col-span-8 lg:col-span-9 p-6 lg:p-8">

                <!-- ==================== TAB 1: INFORMASI AKUN (SKPL-UI05 Bawah) ==================== -->
                <div id="tab-profile" class="tab-content space-y-6">
                    <div class="border-b border-zinc-800 pb-3">
                        <h2 class="text-lg font-bold text-white font-brand">Informasi Akun</h2>
                        <p class="text-xs text-zinc-400">Atur avatar, nama pengguna, email, dan status visibilitas akun Anda.</p>
                    </div>

                    <!-- Avatar Foto Profil di Tengah (Persis Mockup Page 37 Bawah) -->
                    <div class="flex flex-col items-center justify-center pt-2 pb-4">
                        <div class="relative group">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                class="w-24 h-24 rounded-full object-cover border-2 border-red-600 shadow-xl shadow-red-950/40">
                            
                            <!-- Camera Overlay Button for Avatar Upload -->
                            <label for="avatar_input" title="Ganti Foto Profil"
                                class="absolute bottom-0 right-0 bg-red-600 hover:bg-red-500 text-white p-2 rounded-full cursor-pointer shadow-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </label>
                        </div>

                        <!-- Form Auto-Submit Upload Avatar -->
                        <form id="avatar_form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="avatar_input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                                onchange="document.getElementById('avatar_form').submit();">
                        </form>
                        <span class="text-[11px] text-zinc-500 mt-2">Klik ikon kamera untuk mengganti foto avatar</span>
                    </div>

                    <!-- Form Informasi Akun -->
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-xs font-semibold text-zinc-300 mb-1">Username / Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                            @error('name')
                                <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                            @error('email')
                                <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role Akun -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Peran Akun (Role)</label>
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ ucfirst($user->role) }}" disabled
                                    class="w-full px-3.5 py-2.5 bg-[#121318]/60 border border-zinc-800 rounded-lg text-zinc-400 text-xs cursor-not-allowed">
                                @if($user->isAdmin())
                                    <span class="px-3 py-2 bg-red-950 border border-red-700 text-red-300 rounded-lg text-xs font-bold whitespace-nowrap">
                                        🛡️ Admin
                                    </span>
                                @elseif($user->isKreator())
                                    <span class="px-3 py-2 bg-amber-950 border border-amber-700 text-amber-300 rounded-lg text-xs font-bold whitespace-nowrap">
                                        🎨 Kreator
                                    </span>
                                @else
                                    <span class="px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-300 rounded-lg text-xs font-bold whitespace-nowrap">
                                        📖 Pembaca
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Privasi / Status Akun (Sesuai Mockup UI05) -->
                        <div class="pt-2 border-t border-zinc-800/80 flex items-center justify-between">
                            <div>
                                <span class="block text-xs font-semibold text-zinc-300">Status Keaktifan Akun</span>
                                <span class="text-[11px] text-zinc-500">Akun terverifikasi dan aktif di sistem KOMIQ</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-950/80 border border-emerald-800 text-emerald-300 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Aktif
                            </span>
                        </div>

                        <div class="pt-3">
                            <button type="submit"
                                class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs shadow-lg shadow-red-900/40 uppercase tracking-wider transition">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==================== TAB 2: KEAMANAN & SANDI ==================== -->
                <div id="tab-security" class="tab-content hidden space-y-6">
                    <div class="border-b border-zinc-800 pb-3">
                        <h2 class="text-lg font-bold text-white font-brand">Keamanan & Kata Sandi</h2>
                        <p class="text-xs text-zinc-400">Pastikan akun Anda terlindungi dengan mengganti sandi secara berkala.</p>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-xs font-semibold text-zinc-300 mb-1">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" required
                                placeholder="••••••••"
                                class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                            @error('current_password')
                                <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password" class="block text-xs font-semibold text-zinc-300 mb-1">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" required
                                placeholder="Minimal 8 karakter"
                                class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                            @error('new_password')
                                <p class="mt-1 text-[11px] text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-semibold text-zinc-300 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                placeholder="Ulangi password baru"
                                class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition">
                        </div>

                        <div class="pt-3">
                            <button type="submit"
                                class="w-full py-2.5 px-4 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-lg text-xs transition duration-200 uppercase tracking-wider">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==================== TAB 3: SALDO & KOIN (Ikon Uang) ==================== -->
                <div id="tab-coins" class="tab-content hidden space-y-6">
                    <div class="border-b border-zinc-800 pb-3">
                        <h2 class="text-lg font-bold text-white font-brand">Saldo & Koin Akun</h2>
                        <p class="text-xs text-zinc-400">Informasi saldo koin simulasi untuk membuka Chapter Premium komik.</p>
                    </div>

                    <div class="bg-[#121318] border border-zinc-800 rounded-xl p-6 text-center max-w-lg mx-auto space-y-3">
                        <span class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Total Saldo Koin</span>
                        <div class="flex items-center justify-center gap-2 text-4xl font-extrabold text-amber-400 font-brand">
                            <span>💵</span>
                            <span>{{ number_format($user->coin_balance) }}</span>
                        </div>
                        <p class="text-xs text-zinc-400">
                            Koin dapat digunakan untuk membuka bab premium komik favorit Anda yang dikelola oleh tim KOMIQ.
                        </p>
                    </div>
                </div>

                <!-- ==================== TAB 4: PENGATURAN TAMPILAN (SKPL-UI05 Atas) ==================== -->
                <div id="tab-appearance" class="tab-content hidden space-y-6">
                    <div class="border-b border-zinc-800 pb-3">
                        <h2 class="text-lg font-bold text-white font-brand">Pengaturan Tampilan</h2>
                        <p class="text-xs text-zinc-400">Sesuaikan tema aksen warna dan preferensi visual pembaca.</p>
                    </div>

                    <div class="space-y-5 max-w-lg mx-auto">
                        <!-- Warna Tema Aksen (Persis Gambar SKPL-UI05) -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-2">Warna Tema Aksen</label>
                            <div class="grid grid-cols-5 gap-3">
                                <!-- Red (Active) -->
                                <button type="button" class="h-10 rounded-lg bg-red-600 ring-2 ring-white flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-red-900/50">
                                    ✓
                                </button>
                                <!-- Cyan -->
                                <button type="button" class="h-10 rounded-lg bg-cyan-500 opacity-60 hover:opacity-100 transition"></button>
                                <!-- Emerald -->
                                <button type="button" class="h-10 rounded-lg bg-emerald-500 opacity-60 hover:opacity-100 transition"></button>
                                <!-- Amber -->
                                <button type="button" class="h-10 rounded-lg bg-amber-500 opacity-60 hover:opacity-100 transition"></button>
                                <!-- Pink -->
                                <button type="button" class="h-10 rounded-lg bg-pink-500 opacity-60 hover:opacity-100 transition"></button>
                            </div>
                            <span class="text-[11px] text-zinc-400 mt-2 block">Tema default platform KOMIQ adalah Hitam - Merah Crimson.</span>
                        </div>
                    </div>
                </div>

                <!-- ==================== TAB 5: BAHASA (SKPL-UI05) ==================== -->
                <div id="tab-language" class="tab-content hidden space-y-6">
                    <div class="border-b border-zinc-800 pb-3">
                        <h2 class="text-lg font-bold text-white font-brand">Preferensi Bahasa</h2>
                        <p class="text-xs text-zinc-400">Atur bahasa antarmuka dan preferensi teks bacaan.</p>
                    </div>

                    <div class="space-y-4 max-w-lg mx-auto">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Bahasa Antarmuka</label>
                            <select class="w-full px-3.5 py-2.5 bg-[#121318] border border-zinc-700/80 rounded-lg text-white text-xs">
                                <option selected>Bahasa Indonesia (Default)</option>
                                <option>English (US)</option>
                            </select>
                        </div>
                    </div>
                </div>

            </section>

        </div>

    </main>

    <!-- Bottom Red Accent Stripe (Sesuai Desain SKPL) -->
    <div class="w-full h-2 bg-red-600 shadow-[0_-2px_10px_rgba(220,38,38,0.4)] mt-8"></div>

    <!-- Tab Switching Script -->
    <script>
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Remove active style from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-red-600/15', 'border', 'border-red-800/60', 'text-red-400');
                btn.classList.add('text-zinc-400');
            });

            // Show selected content
            const activeContent = document.getElementById(tabId);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            // Highlight selected button
            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('text-zinc-400');
                activeBtn.classList.add('bg-red-600/15', 'border', 'border-red-800/60', 'text-red-400');
            }
        }
    </script>
</body>
</html>