@extends('layouts.app')

@section('title', 'Sign In - KOMIQ')
@section('inline-errors', true)

@section('content')
<div class="card max-w-md mx-auto my-8 p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-white">Sign In</h1>
        <p class="text-xs text-zinc-400 mt-1.5">
            Tidak punya akun?
            <a href="{{ route('register') }}" class="font-bold ml-1 underline underline-offset-2">Sign up.</a>
        </p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="email@email.com" class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex justify-between items-center">
                <label for="password" class="form-label">Password</label>
                <span class="text-[11px] text-zinc-500 hover:text-red-400 cursor-pointer transition">Lupa Password?</span>
            </div>
            <input type="password" id="password" name="password" required
                placeholder="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-xs text-zinc-400 cursor-pointer select-none">
            <input type="checkbox" name="remember" class="accent-red-600">
            Ingat saya
        </label>

        <button type="submit" class="btn btn-primary w-full">Sign In</button>
    </form>

    <div class="mt-6 pt-5 border-t border-[var(--border-color)] text-center">
        <span class="text-xs text-zinc-400 block mb-2 font-medium">Akun Pengujian Demo (Klik Cepat):</span>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="quickFill('admin@komiq.com', 'password123')" class="btn btn-secondary btn-sm">
                <x-icon name="shield" /> Admin
            </button>
            <button type="button" onclick="quickFill('kreator@komiq.com', 'password123')" class="btn btn-secondary btn-sm">
                <x-icon name="palette" /> Kreator
            </button>
            <button type="button" onclick="quickFill('user@komiq.com', 'password123')" class="btn btn-secondary btn-sm">
                <x-icon name="book-open" /> User
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function quickFill(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
