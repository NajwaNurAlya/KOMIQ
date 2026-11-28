@extends('layouts.app')

@section('title', $comic->title . ' - KOMIQ')

@section('content')
@php
    $hari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    $pertama = $comic->chapters->first();
    $terbaru = $comic->chapters->last();
    $cover = $comic->cover_image ? asset('storage/' . $comic->cover_image) : null;
    $kotak = 'overflow-hidden rounded-xl border border-[var(--border-color)] bg-[var(--bg-card)]';
    $judulKotak = 'border-b border-[var(--border-color)] bg-[var(--bg-secondary)] px-5 py-4 text-lg';
@endphp

<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
    <div class="min-w-0 space-y-6">
        <section>
            <div class="h-44 overflow-hidden rounded-xl bg-[var(--bg-card)] sm:h-56">
                @if($comic->banner_image)
                    <img src="{{ asset('storage/' . $comic->banner_image) }}" alt="" class="h-full w-full object-cover">
                @elseif($cover)
                    <img src="{{ $cover }}" alt="" class="h-full w-full scale-110 object-cover opacity-60 blur-sm">
                @endif
            </div>
            <div class="flex gap-4 px-4">
                <div class="relative z-10 -mt-20 aspect-[3/4] w-28 shrink-0 overflow-hidden rounded-md border-2 border-[var(--bg-primary)] bg-[var(--bg-card)] shadow-lg sm:w-32">
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $comic->title }}" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="min-w-0 flex-1 pt-3">
                    <h1 class="text-lg uppercase leading-tight sm:text-xl">{{ $comic->title }}</h1>
                    <p class="mt-1 text-[11px] text-zinc-400">
                        Komik · {{ $comic->genres->pluck('name')->join(', ') }} · {{ $comic->status === 'completed' ? 'Tamat' : 'Berjalan' }}
                    </p>
                    @if($pertama)
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('comics.chapters.show', [$comic->id, $pertama->chapter_number]) }}" class="btn btn-primary btn-sm">Baca Bab {{ $pertama->chapter_number }}</a>
                            <a href="{{ route('comics.chapters.show', [$comic->id, $terbaru->chapter_number]) }}" class="btn btn-sm border border-zinc-500 text-zinc-100 hover:bg-zinc-800 hover:text-white">Bab Terbaru</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="{{ $kotak }}">
            <div class="flex items-center justify-between {{ $judulKotak }}">
                <h2 class="text-lg">{{ $comic->chapters->count() }} Chapter</h2>
                @if($comic->chapters->count() > 1)
                    <button type="button" id="urutkan" class="text-zinc-300 hover:text-white" aria-label="Balik urutan chapter" title="Balik urutan">
                        <x-icon name="arrow-up-down" size="1.15em" />
                    </button>
                @endif
            </div>
            <div id="daftar-chapter" class="flex flex-col divide-y divide-[var(--border-color)]">
                @forelse($comic->chapters as $chapter)
                    @php $thumb = $chapter->pages[0] ?? $comic->cover_image; @endphp
                    <a href="{{ route('comics.chapters.show', [$comic->id, $chapter->chapter_number]) }}" class="flex items-center gap-4 pr-4 text-zinc-100 hover:bg-[var(--bg-card-hover)] hover:text-white">
                        <div class="h-20 w-20 shrink-0 overflow-hidden bg-[var(--bg-secondary)]">
                            @if($thumb)
                                <img src="{{ asset('storage/' . $thumb) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 py-2">
                            @if($chapter->title !== "Chapter {$chapter->chapter_number}")
                                <p class="text-[11px] text-zinc-400">Chapter {{ $chapter->chapter_number }}</p>
                            @endif
                            <p class="truncate font-semibold">{{ $chapter->title }}</p>
                            <p class="text-xs text-zinc-400">{{ $chapter->published_at?->format('M d, Y') }}</p>
                        </div>
                        @if($chapter->is_premium)
                            <span class="badge badge-premium shrink-0"><x-icon name="coins" /> {{ $chapter->coin_price }}</span>
                        @endif
                    </a>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-zinc-500">Belum ada chapter yang terbit.</p>
                @endforelse
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="{{ $kotak }}">
            <h2 class="{{ $judulKotak }}">Kreator</h2>
            <div class="flex items-center gap-3 px-5 py-4">
                <img src="{{ $comic->user->avatar_url }}" alt="" class="h-9 w-9 rounded-full">
                <span class="font-semibold">{{ $comic->user->name }}</span>
            </div>
        </section>

        <section class="{{ $kotak }}">
            <h2 class="{{ $judulKotak }}">Deskripsi</h2>
            <div class="space-y-4 px-5 py-4 text-sm">
                <p class="whitespace-pre-line leading-relaxed text-zinc-300">{{ $comic->synopsis ?: 'Belum ada deskripsi.' }}</p>
                <p class="flex items-center gap-2 font-semibold">
                    <x-icon name="calendar" />
                    @if($comic->status === 'completed')
                        Tamat
                    @elseif($terbaru?->published_at)
                        Tiap {{ $hari[$terbaru->published_at->dayOfWeekIso] }}
                    @else
                        Belum terbit
                    @endif
                </p>
            </div>
        </section>

        @if($rekomendasi->isNotEmpty())
            <section class="{{ $kotak }}">
                <h2 class="{{ $judulKotak }}">Rekomendasi</h2>
                <div class="divide-y divide-[var(--border-color)]">
                    @foreach($rekomendasi as $lain)
                        <a href="{{ route('comics.show', $lain->id) }}" class="flex items-center gap-4 pr-4 text-zinc-100 hover:bg-[var(--bg-card-hover)] hover:text-white">
                            <div class="h-20 w-20 shrink-0 overflow-hidden bg-[var(--bg-secondary)]">
                                @if($lain->cover_image)
                                    <img src="{{ asset('storage/' . $lain->cover_image) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 py-2">
                                <p class="text-[11px] text-[var(--accent-text)]">{{ $lain->genres->first()?->name }}</p>
                                <p class="line-clamp-2 font-semibold leading-snug">{{ $lain->title }}</p>
                                <p class="text-xs text-zinc-400">{{ $lain->chapters_count }} chapter</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </aside>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('urutkan')?.addEventListener('click', () => {
        const daftar = document.getElementById('daftar-chapter');
        daftar.classList.toggle('flex-col-reverse');
        daftar.classList.toggle('divide-y-reverse');
    });
</script>
@endsection
