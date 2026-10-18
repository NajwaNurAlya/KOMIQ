@extends('layouts.app')

@section('title', 'Pengaturan Pengguna - KOMIQ')

@section('content')
<div class="border-b border-[var(--border-color)] pb-4 mb-6">
    <h1 class="text-2xl font-bold text-white">Pengaturan Pengguna</h1>
    <p class="text-xs text-zinc-400 mt-1">Kelola data profil pribadi, keamanan kata sandi, dan preferensi akun Anda.</p>
</div>

<div class="card p-0 overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[550px]">
    <aside class="md:col-span-4 lg:col-span-3 bg-[var(--bg-secondary)] border-r border-[var(--border-color)] p-4 space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Menu Akun</div>

        <button type="button" onclick="switchTab('tab-profile')" id="btn-tab-profile"
            class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-red-600/15 border border-red-800/60 text-red-400 transition">
            <x-icon name="user" class="text-sm" />
            <span>Informasi Akun</span>
        </button>

        <button type="button" onclick="switchTab('tab-security')" id="btn-tab-security"
            class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <x-icon name="lock" class="text-sm" />
            <span>Keamanan & Sandi</span>
        </button>

        <button type="button" onclick="switchTab('tab-coins')" id="btn-tab-coins"
            class="tab-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <x-icon name="coins" class="text-sm" />
            <span>Saldo & Koin</span>
        </button>
    </aside>

    <section class="md:col-span-8 lg:col-span-9 p-6 lg:p-8">
        <div id="tab-profile" class="tab-content space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Informasi Akun</h2>
                <p class="text-xs text-zinc-400">Atur avatar, nama pengguna, email, dan status visibilitas akun Anda.</p>
            </div>

            <div class="flex flex-col items-center justify-center pt-2 pb-4">
                <div class="relative">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                        class="w-24 h-24 rounded-full object-cover border-2 border-red-600">
                    <label for="avatar_input" title="Ganti Foto Profil"
                        class="absolute bottom-0 right-0 bg-red-600 hover:bg-red-500 text-white p-2 rounded-full cursor-pointer transition">
                        <x-icon name="camera" class="text-sm" />
                    </label>
                </div>

                <form id="avatar_form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="avatar_input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                        onchange="document.getElementById('avatar_form').submit();">
                </form>
                <span class="text-[11px] text-zinc-500 mt-2">Klik ikon kamera untuk mengganti foto avatar</span>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Username / Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <span class="form-label">Peran Akun (Role)</span>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled class="form-control opacity-60 cursor-not-allowed">
                        @if($user->isAdmin())
                            <span class="btn btn-sm whitespace-nowrap bg-red-950 border border-red-700 text-red-300">
                                <x-icon name="shield" /> Admin
                            </span>
                        @elseif($user->isKreator())
                            <span class="btn btn-sm whitespace-nowrap bg-amber-950 border border-amber-700 text-amber-300">
                                <x-icon name="palette" /> Kreator
                            </span>
                        @else
                            <span class="btn btn-sm btn-secondary whitespace-nowrap">
                                <x-icon name="book-open" /> Pembaca
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-[var(--border-color)] flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-semibold text-zinc-300">Status Keaktifan Akun</span>
                        <span class="text-[11px] text-zinc-500">Akun terverifikasi dan aktif di sistem KOMIQ</span>
                    </div>
                    <span class="badge badge-approved">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                    </span>
                </div>

                <button type="submit" class="btn btn-primary w-full">Simpan Perubahan Profil</button>
            </form>
        </div>

        <div id="tab-security" class="tab-content hidden space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Keamanan & Kata Sandi</h2>
                <p class="text-xs text-zinc-400">Pastikan akun Anda terlindungi dengan mengganti sandi secara berkala.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required
                        placeholder="••••••••" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required
                        placeholder="Minimal 8 karakter" class="form-control @error('new_password') is-invalid @enderror">
                    @error('new_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                        placeholder="Ulangi password baru" class="form-control">
                </div>

                <button type="submit" class="btn btn-secondary w-full">Perbarui Kata Sandi</button>
            </form>
        </div>

        <div id="tab-coins" class="tab-content hidden space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Saldo & Koin Akun</h2>
                <p class="text-xs text-zinc-400">Informasi saldo koin simulasi untuk membuka Chapter Premium komik.</p>
            </div>

            <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-xl p-6 text-center max-w-lg mx-auto space-y-3">
                <span class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Total Saldo Koin</span>
                <div class="flex items-center justify-center gap-2 text-4xl font-extrabold text-amber-400 font-brand">
                    <x-icon name="coins" />
                    <span>{{ number_format($user->coin_balance) }}</span>
                </div>
                <p class="text-xs text-zinc-400">
                    Koin dapat digunakan untuk membuka bab premium komik favorit Anda yang dikelola oleh tim KOMIQ.
                </p>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-red-600/15', 'border', 'border-red-800/60', 'text-red-400');
            btn.classList.add('text-zinc-400');
        });

        document.getElementById(tabId)?.classList.remove('hidden');

        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-zinc-400');
            activeBtn.classList.add('bg-red-600/15', 'border', 'border-red-800/60', 'text-red-400');
        }
    }
</script>
@endsection
