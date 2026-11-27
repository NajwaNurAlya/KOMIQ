@extends('layouts.app')

@section('title', 'Kreator Hub - KOMIQ')

@section('content')
<h1 class="mb-1 text-2xl">Kreator Hub</h1>
<p class="mb-6 text-sm text-zinc-400">Komik milikmu. Pilih komik untuk mengelola chapter-nya.</p>

@if($comics->isEmpty())
    <div class="card py-16 text-center text-zinc-400">Kamu belum punya komik.</div>
@else
    <div class="grid grid-cols-2 gap-x-3 gap-y-6 sm:grid-cols-3 md:grid-cols-5">
        @foreach($comics as $comic)
            <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="group block min-w-0 text-zinc-100 hover:text-white">
                <div class="aspect-[3/4] overflow-hidden rounded-md bg-[var(--bg-card)] border border-[var(--border-color)] flex items-center justify-center">
                    @if($comic->cover_image)
                        <img src="{{ asset('storage/' . $comic->cover_image) }}" alt="{{ $comic->title }}" loading="lazy"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    @else
                        <div class="text-zinc-600 flex flex-col items-center">
                            <x-icon name="image" class="text-3xl mb-1" />
                            <span class="text-[10px] text-zinc-500">Tanpa Sampul</span>
                        </div>
                    @endif
                </div>
                <p class="mt-2 truncate text-sm font-semibold">{{ $comic->title }}</p>
                <p class="text-[11px] text-zinc-500">{{ $comic->chapters_count }} chapter · {{ $comic->status === 'completed' ? 'Tamat' : 'Berjalan' }}</p>
            </a>
        @endforeach
    </div>
@endif
@endsection
