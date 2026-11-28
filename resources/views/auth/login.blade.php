@extends('layouts.app')

@section('title', 'Masuk - KOMIQ')
@section('inline-errors', true)

@section('content')
<div class="card max-w-md mx-auto my-8 p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-white">Masuk</h1>
        <p class="text-xs text-zinc-400 mt-1.5">
            Tidak punya akun?
            <a href="{{ route('register') }}" class="font-bold ml-1 underline underline-offset-2">Daftar.</a>
        </p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="space-y-4">

        <div>
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="email@email.com" class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
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

        <button type="submit" class="btn btn-primary w-full">Masuk</button>
    </form>
</div>
@endsection
