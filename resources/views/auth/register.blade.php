@extends('layouts.app')

@section('title', 'Sign Up - KOMIQ')
@section('inline-errors', true)

@section('content')
<div class="card max-w-md mx-auto my-8 p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-white">Sign Up</h1>
        <p class="text-xs text-zinc-400 mt-1.5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold ml-1 underline underline-offset-2">Sign In.</a>
        </p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="form-label">Nama Lengkap (Username)</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                placeholder="Nama Anda" class="form-control @error('name') is-invalid @enderror">
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                placeholder="email@email.com" class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <span class="form-label">Daftar Sebagai Peran</span>
            <div class="grid grid-cols-2 gap-2">
                <label class="form-control flex items-center gap-2 cursor-pointer hover:border-red-800">
                    <input type="radio" name="role" value="user" {{ old('role', 'user') === 'user' ? 'checked' : '' }} class="accent-red-600">
                    <x-icon name="book-open" /> Pembaca
                </label>
                <label class="form-control flex items-center gap-2 cursor-pointer hover:border-red-800">
                    <input type="radio" name="role" value="kreator" {{ old('role') === 'kreator' ? 'checked' : '' }} class="accent-red-600">
                    <x-icon name="palette" /> Kreator
                </label>
            </div>
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" required
                placeholder="Minimal 8 karakter" class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                placeholder="Ulangi password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-full">Sign Up</button>
    </form>

    <p class="mt-5 text-[11px] text-zinc-400 text-center leading-relaxed">
        Dengan berlanjut, anda setuju dengan <a href="#" class="text-zinc-300 underline hover:text-white">Syarat Pengguna</a> dan <a href="#" class="text-zinc-300 underline hover:text-white">Privasi</a> platform KOMIQ.
    </p>
</div>
@endsection
