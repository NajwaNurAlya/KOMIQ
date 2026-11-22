@extends('layouts.app')

@section('title', 'Top Up Koin - KOMIQ')

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $status = ['success' => ['Berhasil', 'badge-approved'], 'pending' => ['Menunggu', 'badge-pending'], 'failed' => ['Gagal', 'badge-rejected']];
@endphp

<div class="mx-auto max-w-3xl">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl">Top Up Koin</h1>
            <p class="text-sm text-zinc-400">1 koin = Rp 100. Metode pembayaran dipilih di halaman Midtrans.</p>
        </div>
        <p class="flex items-center gap-2 text-sm text-zinc-400">
            Saldo <span class="flex items-center gap-1 text-lg font-bold text-amber-300"><x-icon name="coins" /> {{ number_format($saldo, 0, ',', '.') }}</span>
        </p>
    </div>

    <form action="{{ route('coins.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach(\App\Http\Controllers\CoinController::PAKET as $kode => $paket)
                <label class="cursor-pointer rounded-xl border border-[var(--border-color)] bg-[var(--bg-card)] p-4 text-center hover:border-zinc-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10">
                    <input type="radio" name="paket" value="{{ $kode }}" class="sr-only" @checked(old('paket', 'p500') === $kode)>
                    <p class="text-2xl font-bold text-white">{{ number_format($paket['koin'] + $paket['bonus'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-zinc-400">{{ $paket['bonus'] ? number_format($paket['koin'], 0, ',', '.') . ' + ' . $paket['bonus'] . ' bonus' : 'koin' }}</p>
                    <p class="mt-3 text-sm font-semibold text-zinc-100">{{ $rp($paket['harga']) }}</p>
                </label>
            @endforeach
        </div>
        @error('paket') <p class="form-error">{{ $message }}</p> @enderror

        <button type="submit" class="btn btn-primary mt-4 w-full">Lanjut ke Pembayaran</button>
    </form>

    <h2 class="mb-3 mt-10 text-lg">Riwayat Transaksi</h2>
    <div class="divide-y divide-[var(--border-color)] rounded-xl border border-[var(--border-color)] bg-[var(--bg-card)]">
        @forelse($riwayat as $trx)
            <div class="flex items-center gap-4 px-4 py-3 text-sm">
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-white">{{ $trx->description }}</p>
                    <p class="text-xs text-zinc-500">{{ $trx->created_at->translatedFormat('d M Y, H:i') }} · {{ $rp($trx->price ?? 0) }}</p>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1">
                    <span class="badge {{ $status[$trx->status][1] ?? 'badge-draft' }}">{{ $status[$trx->status][0] ?? $trx->status }}</span>
                    @if($trx->status === 'pending')
                        <a href="{{ route('coins.finish', ['order_id' => $trx->order_id]) }}" class="text-xs">Cek Status</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-4 py-8 text-center text-sm text-zinc-500">Belum ada transaksi.</p>
        @endforelse
    </div>
</div>
@endsection
