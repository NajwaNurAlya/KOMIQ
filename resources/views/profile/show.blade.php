@extends('layouts.app')

@section('title', 'Pengaturan Pengguna - KOMIQ')
@section('inline-errors', true)

@section('content')
<div class="border-b border-[var(--border-color)] pb-4 mb-6">
    <h1 class="text-2xl font-bold text-white">Pengaturan Pengguna</h1>
    <p class="text-xs text-zinc-400 mt-1">Kelola profil, kata sandi, dan saldo koin Anda.</p>
</div>

<div class="card p-0 overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[550px]">
    <aside class="md:col-span-4 lg:col-span-3 bg-[var(--bg-secondary)] border-r border-[var(--border-color)] p-4 space-y-1">
        <div class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Menu Akun</div>

        @foreach(['biodata' => ['user', 'Informasi Akun'], 'keamanan' => ['lock', 'Keamanan & Sandi'], 'koin' => ['coins', 'Saldo & Koin']] as $kunci => [$ikon, $label])
            <a href="{{ route('profile.show', $kunci === 'biodata' ? null : $kunci) }}"
                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold border transition {{ $tab === $kunci ? 'bg-red-600/15 border-red-800/60 text-red-400 hover:text-red-400' : 'border-transparent text-zinc-400 hover:text-white hover:bg-zinc-800/60' }}">
                <x-icon name="{{ $ikon }}" class="text-sm" />
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </aside>

    <section class="md:col-span-8 lg:col-span-9 p-6 lg:p-8">
        @if($tab === 'biodata')
        <div class="space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Informasi Akun</h2>
                <p class="text-xs text-zinc-400">Atur nama dan email akun Anda.</p>
            </div>

            <div class="flex justify-center pt-2 pb-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                    class="w-24 h-24 rounded-full object-cover border-2 border-red-600">
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
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

                <button type="submit" class="btn btn-primary w-full">Simpan Perubahan Profil</button>
            </form>
        </div>
        @elseif($tab === 'keamanan')
        <div class="space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Keamanan & Kata Sandi</h2>
                <p class="text-xs text-zinc-400">Pastikan akun Anda terlindungi dengan mengganti sandi secara berkala.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-lg mx-auto">
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
        @else
        <div class="space-y-6">
            <div class="border-b border-[var(--border-color)] pb-3">
                <h2 class="text-lg font-bold text-white">Saldo & Koin Akun</h2>
                <p class="text-xs text-zinc-400">Koin dipakai untuk membuka chapter premium.</p>
            </div>

            <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-xl p-6 text-center max-w-lg mx-auto space-y-3">
                <span class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Total Saldo Koin</span>
                <div class="flex items-center justify-center gap-2 text-4xl font-extrabold text-amber-400 font-brand">
                    <x-icon name="coins" />
                    <span>{{ number_format($user->coin_balance) }}</span>
                </div>
                <a href="{{ route('coins.index') }}" class="btn btn-primary"><x-icon name="coins" /> Top Up Koin</a>
            </div>
        </div>
        @endif
    </section>
</div>
@endsection
