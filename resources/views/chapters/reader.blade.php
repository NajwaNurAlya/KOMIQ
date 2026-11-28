@extends('layouts.app')

@section('title', 'Membaca ' . ($comic->title ?? 'Komik') . ' - Chapter ' . $chapter->chapter_number)
@section('tanpa-footer', true)
@section('inline-errors', true)

@section('styles')
<style>
    body { padding-bottom: 3.5rem; }
    .comic-page-img { width: 100%; height: auto; display: block; }
</style>
@endsection

@section('content')
@php
    $cover = $comic->cover_image ? asset('storage/' . $comic->cover_image) : null;
@endphp

<div id="tata-letak" class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <div class="mx-auto w-full max-w-[760px] min-w-0">
        @if(! $chapter->isApproved())
            <div class="alert alert-warning">
                <x-icon name="eye" />
                <div>
                    <strong>Mode Pratinjau Kreator / Admin:</strong> Chapter ini saat ini berstatus
                    <x-chapter-status :chapter="$chapter" style="margin: 0 4px;" />
                    dan belum dapat diakses oleh publik luas.
                </div>
            </div>
        @endif

        @if($isUnlocked)
            @if(is_array($chapter->pages) && count($chapter->pages) > 0)
                <div class="overflow-hidden border border-[var(--border-color)] bg-black">
                    @foreach($chapter->pages as $index => $pagePath)
                        <img src="{{ asset('storage/' . $pagePath) }}" alt="Halaman {{ $index + 1 }}" class="comic-page-img" loading="lazy">
                    @endforeach
                </div>
            @else
                <div class="card py-16 text-center text-zinc-400">
                    <div class="mb-2 text-5xl"><x-icon name="book-open" stroke-width="1.5" /></div>
                    <p class="text-lg text-zinc-100">Belum ada gambar halaman pada chapter ini.</p>
                </div>
            @endif
        @else
            <div class="card px-8 py-16 text-center">
                <div class="mb-4 text-6xl text-zinc-500"><x-icon name="lock" stroke-width="1.5" /></div>
                <span class="badge badge-premium mb-4">Chapter Premium</span>
                <h2 class="mb-2 mt-4 text-3xl text-white">Chapter Terkunci</h2>
                <p class="mx-auto mb-3 max-w-md text-sm text-zinc-400">
                    Chapter ini merupakan konten eksklusif dan membutuhkan
                    <strong class="text-amber-400">{{ $chapter->coin_price }} Koin</strong> untuk dibuka.
                </p>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary"><x-icon name="user" /> Masuk untuk Membuka</a>
                @else
                    <p class="mb-6 text-sm text-zinc-500">
                        Saldo Koin Anda saat ini: <strong class="text-amber-400">{{ $userCoinBalance }} Koin</strong>
                    </p>

                    @if($userCoinBalance >= $chapter->coin_price)
                        <form action="{{ route('comics.chapters.unlock', [$comic->id, $chapter->id]) }}" method="POST">
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="coins" /> Buka Chapter dengan {{ $chapter->coin_price }} Koin
                            </button>
                        </form>
                    @else
                        <div class="alert alert-error mx-auto mb-0 max-w-md justify-center">
                            <x-icon name="triangle-alert" /> Saldo koin Anda tidak mencukupi (Kurang {{ $chapter->coin_price - $userCoinBalance }} koin).
                        </div>
                        <a href="{{ route('coins.index') }}" class="btn btn-primary mt-4"><x-icon name="coins" /> Top Up Koin</a>
                    @endif
                @endguest
            </div>
        @endif
    </div>

    <aside id="panel-bab" class="flex flex-col overflow-hidden rounded-xl border border-[var(--border-color)] bg-[var(--bg-card)] max-lg:fixed max-lg:inset-x-3 max-lg:bottom-16 max-lg:top-20 max-lg:z-40 max-lg:hidden max-lg:shadow-2xl lg:sticky lg:top-20 lg:max-h-[calc(100vh-9rem)]">
        <div class="h-24 shrink-0 overflow-hidden bg-[var(--bg-secondary)]">
            @if($comic->banner_image)
                <img src="{{ asset('storage/' . $comic->banner_image) }}" alt="" class="h-full w-full object-cover opacity-80">
            @elseif($cover)
                <img src="{{ $cover }}" alt="" class="h-full w-full scale-110 object-cover opacity-60 blur-sm">
            @endif
        </div>
        <div class="flex shrink-0 gap-3 px-4">
            <div class="relative z-10 -mt-10 aspect-[3/4] w-20 shrink-0 overflow-hidden rounded-md border-2 border-[var(--bg-card)] bg-[var(--bg-secondary)]">
                @if($cover)
                    <img src="{{ $cover }}" alt="{{ $comic->title }}" class="h-full w-full object-cover">
                @endif
            </div>
            <div class="min-w-0 pt-2">
                <p class="line-clamp-2 text-sm font-bold uppercase leading-snug">{{ $comic->title }}</p>
                <p class="text-[11px] text-zinc-400">{{ $comic->status === 'completed' ? 'Tamat' : 'Berjalan' }}</p>
            </div>
        </div>
        <div class="shrink-0 space-y-2 p-4">
            <a href="{{ route('comics.show', $comic->id) }}" class="btn btn-sm w-full border border-zinc-500 text-zinc-100 hover:bg-zinc-800 hover:text-white">
                <x-icon name="book-open" /> Detail Komik
            </a>
            @if($isCreator)
                <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-sm w-full border border-zinc-500 text-zinc-100 hover:bg-zinc-800 hover:text-white">
                    <x-icon name="list" /> Kelola Chapter
                </a>
            @endif
        </div>

        <div class="flex shrink-0 items-center justify-between border-y border-[var(--border-color)] bg-[var(--bg-secondary)] px-4 py-3">
            <h2 class="text-sm">{{ $allApprovedChapters->count() }} Bab</h2>
            @if($allApprovedChapters->count() > 1)
                <button type="button" id="urutkan" class="text-zinc-300 hover:text-white" aria-label="Balik urutan bab" title="Balik urutan">
                    <x-icon name="arrow-up-down" />
                </button>
            @endif
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <div id="daftar-bab" class="flex flex-col divide-y divide-[var(--border-color)]">
            @foreach($allApprovedChapters as $bab)
                @php
                    $thumb = $bab->pages[0] ?? $comic->cover_image;
                    $aktif = $bab->id === $chapter->id;
                @endphp
                <a href="{{ route('comics.chapters.show', [$comic->id, $bab->chapter_number]) }}"
                   class="flex items-center gap-3 pr-3 text-zinc-100 hover:bg-[var(--bg-card-hover)] hover:text-white {{ $aktif ? 'bg-[var(--bg-card-hover)] shadow-[inset_3px_0_0_var(--accent-primary)]' : '' }}">
                    <div class="h-16 w-16 shrink-0 overflow-hidden bg-[var(--bg-secondary)]">
                        @if($thumb)
                            <img src="{{ asset('storage/' . $thumb) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1 py-2">
                        <p class="truncate text-sm font-semibold {{ $aktif ? 'text-[var(--accent-text)]' : '' }}">Chapter {{ $bab->chapter_number }}</p>
                        <p class="text-xs text-zinc-400">{{ $bab->published_at?->format('M d, Y') }}</p>
                    </div>
                    @if($bab->is_premium)
                        <span class="badge badge-premium shrink-0"><x-icon name="coins" /> {{ $bab->coin_price }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        </div>
    </aside>
</div>

<dialog id="modal-komentar" class="w-[min(40rem,calc(100%-2rem))] rounded-xl border border-[var(--border-color)] bg-[var(--bg-card)] p-0 text-zinc-100 backdrop:bg-black/70">
    <div class="flex max-h-[85vh] flex-col">
        <div class="flex shrink-0 items-center justify-between border-b border-[var(--border-color)] px-5 py-4">
            <h2 class="text-lg">Komentar ({{ $comments->count() }})</h2>
            <button type="button" id="tutup-komen" class="text-zinc-400 hover:text-white" aria-label="Tutup komentar"><x-icon name="x" size="1.25em" /></button>
        </div>

        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-5 py-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @forelse($comments as $comment)
                <div class="flex gap-3">
                    <img src="{{ $comment->user->avatar_url }}" alt="" class="h-9 w-9 shrink-0 rounded-full">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-semibold text-zinc-100">{{ $comment->user->name }}</span>
                            <span class="text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                            @auth
                                @if(auth()->id() !== $comment->user_id && ! auth()->user()->isAdmin())
                                    <form action="{{ route('comments.report', $comment->id) }}" method="POST" class="ml-auto"
                                          onsubmit="return confirm('Laporkan komentar ini untuk ditinjau Admin?')">
                                        @csrf
                                        <button type="submit" class="text-zinc-500 hover:text-amber-300" aria-label="Laporkan komentar" title="Laporkan komentar"><x-icon name="triangle-alert" /></button>
                                    </form>
                                @endif
                                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="{{ auth()->id() === $comment->user_id ? 'ml-auto' : '' }}"
                                          onsubmit="return confirm('Hapus komentar ini?')">
                                        @method('DELETE')
                                        <button type="submit" class="text-zinc-500 hover:text-red-400" aria-label="Hapus komentar"><x-icon name="trash-2" /></button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                        <p class="mt-1 whitespace-pre-line break-words text-sm text-zinc-300">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-zinc-500">Belum ada komentar.</p>
            @endforelse
        </div>

        @if($chapter->isApproved())
            <div class="shrink-0 border-t border-[var(--border-color)] px-5 py-4">
                @auth
                    <form action="{{ route('comics.chapters.comments.store', [$comic->id, $chapter->id]) }}" method="POST">
                        <textarea name="content" rows="2" maxlength="1000" placeholder="Tulis komentar..."
                            class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="btn btn-primary btn-sm"><x-icon name="send" /> Kirim</button>
                        </div>
                    </form>
                @else
                    <p class="text-sm text-zinc-400"><a href="{{ route('login') }}">Masuk</a> untuk menulis komentar.</p>
                @endauth
            </div>
        @endif
    </div>
</dialog>

<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-[var(--border-color)] bg-[var(--bg-secondary)]">
    <div class="mx-auto flex h-14 max-w-[1200px] items-center justify-between gap-4 px-6">
        <div class="flex min-w-0 flex-1 items-center gap-3 text-sm">
            @if($cover)
                <img src="{{ $cover }}" alt="" class="h-9 w-9 shrink-0 rounded object-cover">
            @endif
            <a href="{{ route('comics.show', $comic->id) }}" class="hidden max-w-[14rem] truncate text-zinc-300 hover:text-white sm:block" title="{{ $comic->title }}">{{ $comic->title }}</a>
            <span class="hidden shrink-0 text-zinc-500 sm:inline"><x-icon name="chevron-right" /></span>
            <span class="shrink-0 font-semibold text-white">Chapter {{ $chapter->chapter_number }}</span>
        </div>

        <div class="flex shrink-0 items-center gap-2 text-sm">
            @if($prevChapter)
                <a href="{{ route('comics.chapters.show', [$comic->id, $prevChapter->chapter_number]) }}" class="rounded p-1 text-zinc-300 hover:bg-zinc-700 hover:text-white" aria-label="Chapter sebelumnya"><x-icon name="chevron-left" /></a>
            @else
                <span class="p-1 text-zinc-600"><x-icon name="chevron-left" /></span>
            @endif
            <span class="text-zinc-300">#{{ $chapter->chapter_number }}</span>
            @if($nextChapter)
                <a href="{{ route('comics.chapters.show', [$comic->id, $nextChapter->chapter_number]) }}" class="rounded p-1 text-zinc-300 hover:bg-zinc-700 hover:text-white" aria-label="Chapter berikutnya"><x-icon name="chevron-right" /></a>
            @else
                <span class="p-1 text-zinc-600"><x-icon name="chevron-right" /></span>
            @endif
        </div>

        <div class="flex justify-end gap-1 sm:flex-1">
            <button type="button" id="tombol-komen" class="flex flex-col items-center rounded px-2 py-1 text-[10px] text-zinc-300 hover:bg-zinc-700 hover:text-white">
                <x-icon name="message-square" size="1.25em" /> Komen
            </button>
            <button type="button" id="tombol-list" class="flex flex-col items-center rounded px-2 py-1 text-[10px] text-zinc-300 hover:bg-zinc-700 hover:text-white">
                <x-icon name="list" size="1.25em" /> List
            </button>
        </div>
    </div>
</nav>
@endsection

@section('scripts')
<script>
    const panelBab = document.getElementById('panel-bab');
    const modalKomen = document.getElementById('modal-komentar');

    document.getElementById('tombol-komen').addEventListener('click', () => modalKomen.showModal());
    document.getElementById('tutup-komen').addEventListener('click', () => modalKomen.close());
    modalKomen.addEventListener('click', (e) => {
        if (e.target === modalKomen) modalKomen.close();
    });
    if (location.hash === '#komentar' || @json($errors->has('content'))) {
        modalKomen.showModal();
    }

    document.getElementById('tombol-list').addEventListener('click', () => {
        if (window.matchMedia('(min-width: 1024px)').matches) {
            panelBab.classList.toggle('lg:hidden');
            document.getElementById('tata-letak').classList.toggle('lg:grid-cols-[minmax(0,1fr)_320px]');
        } else {
            panelBab.classList.toggle('max-lg:hidden');
        }
    });

    document.getElementById('urutkan')?.addEventListener('click', () => {
        const daftar = document.getElementById('daftar-bab');
        daftar.classList.toggle('flex-col-reverse');
        daftar.classList.toggle('divide-y-reverse');
    });
</script>
@endsection
