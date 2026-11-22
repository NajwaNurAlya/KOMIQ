@extends('layouts.app')

@section('title', 'Jelajah Komik - KOMIQ')

@section('styles')
<style>
    .pilih-filter {
        appearance: none;
        cursor: pointer;
        padding-right: 2.75rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a1a1aa' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1rem;
    }
</style>
@endsection

@section('content')
@php
    $pilihan = 'form-control pilih-filter py-2 text-xs';
@endphp

<form action="{{ route('comics.index') }}" method="GET" class="mb-10">
    <div class="flex gap-3">
        <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400"><x-icon name="search" /></span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul atau sinopsis..." class="form-control pl-10">
        </div>
        <button type="button" id="tombol-filter" class="btn btn-primary shrink-0">
            <span id="label-filter" class="hidden sm:inline">Sembunyikan Filter</span><span class="sm:hidden">Filter</span>
        </button>
    </div>

    <div id="panel-filter" class="mt-4">
        <div class="grid gap-4 sm:grid-cols-3">
            <label class="text-xs text-zinc-400">
                Genre
                <select name="genre" class="{{ $pilihan }} mt-1">
                    <option value="">Semua</option>
                    @foreach($genres as $genre)
                        <option value="{{ $genre->slug }}" @selected(request('genre') === $genre->slug)>{{ $genre->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs text-zinc-400">
                Kreator
                <select name="kreator" class="{{ $pilihan }} mt-1">
                    <option value="">Semua</option>
                    @foreach($kreators as $kreator)
                        <option value="{{ $kreator->id }}" @selected((string) request('kreator') === (string) $kreator->id)>{{ $kreator->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs text-zinc-400">
                Status
                <select name="status" class="{{ $pilihan }} mt-1">
                    <option value="">Semua</option>
                    <option value="ongoing" @selected(request('status') === 'ongoing')>Berjalan</option>
                    <option value="completed" @selected(request('status') === 'completed')>Tamat</option>
                </select>
            </label>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            @if(request()->hasAny(['search', 'genre', 'kreator', 'status']))
                <a href="{{ route('comics.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
            <button type="submit" class="btn btn-primary btn-sm"><x-icon name="search" /> Cari</button>
        </div>
    </div>
</form>

<div class="mb-4 flex items-baseline gap-2">
    <h1 class="text-2xl uppercase">{{ request('search') ? 'Hasil "' . request('search') . '"' : 'Semua Komik' }}</h1>
    <span class="text-sm text-zinc-400">| {{ $comics->total() }} Serial</span>
</div>

@if($comics->isEmpty())
    <div class="card py-16 text-center text-zinc-400">Tidak ada komik yang cocok dengan pencarianmu.</div>
@else
    <div class="grid grid-cols-3 gap-x-3 gap-y-6 sm:grid-cols-4 md:grid-cols-6">
        @foreach($comics as $comic)
            <x-comic-card :comic="$comic" :info="$comic->chapters_count . ' chapter · ' . ($comic->status === 'completed' ? 'Tamat' : 'Berjalan')" />
        @endforeach
    </div>

    @if($comics->hasPages())
        <div class="mt-10 flex items-center justify-center gap-4 text-sm">
            @if($comics->onFirstPage())
                <span class="text-zinc-600"><x-icon name="chevron-left" /></span>
            @else
                <a href="{{ $comics->previousPageUrl() }}" class="text-zinc-300 hover:text-white" aria-label="Halaman sebelumnya"><x-icon name="chevron-left" /></a>
            @endif
            <span class="font-semibold text-white">{{ $comics->currentPage() }} / {{ $comics->lastPage() }}</span>
            @if($comics->hasMorePages())
                <a href="{{ $comics->nextPageUrl() }}" class="text-zinc-300 hover:text-white" aria-label="Halaman berikutnya"><x-icon name="chevron-right" /></a>
            @else
                <span class="text-zinc-600"><x-icon name="chevron-right" /></span>
            @endif
        </div>
    @endif
@endif
@endsection

@section('scripts')
<script>
    document.getElementById('tombol-filter').addEventListener('click', () => {
        const panel = document.getElementById('panel-filter');
        panel.hidden = !panel.hidden;
        document.getElementById('label-filter').textContent = panel.hidden ? 'Tampilkan Filter' : 'Sembunyikan Filter';
    });
</script>
@endsection
