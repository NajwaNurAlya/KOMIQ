@extends('layouts.app')

@section('title', 'Membaca ' . ($comic->title ?? 'Komik') . ' - Chapter ' . $chapter->chapter_number)

@section('styles')
<style>
    .reader-container {
        max-width: 820px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .reader-pages {
        background: #000;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 2rem;
    }

    .comic-page-img {
        width: 100%;
        height: auto;
        display: block;
        vertical-align: bottom;
    }

</style>
@endsection

@section('content')
<div class="reader-container">
    @if(! $chapter->isApproved())
        <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
            <x-icon name="eye" />
            <div>
                <strong>Mode Pratinjau Kreator / Admin:</strong> Chapter ini saat ini berstatus 
                <x-chapter-status :chapter="$chapter" style="margin: 0 4px;" /> 
                dan belum dapat diakses oleh publik luas.
            </div>
        </div>
    @endif

    <!-- Top Navigation Bar -->
    <div class="card flex justify-between items-center flex-wrap gap-4 py-4">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">
                {{ $comic->title ?? 'Komik #' . $comic->id }}
            </div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #fff;">
                Chapter {{ $chapter->chapter_number }}: {{ $chapter->title }}
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Dropdown Chapter -->
            <select class="form-control" style="width: auto; padding: 0.4rem 0.8rem; font-size: 0.85rem;" 
                    onchange="if (this.value) window.location.href = this.value;">
                @foreach($allApprovedChapters as $approvedCh)
                    <option value="{{ route('comics.chapters.show', [$comic->id, $approvedCh->chapter_number]) }}"
                            {{ $approvedCh->id === $chapter->id ? 'selected' : '' }}>
                        Ch. {{ $approvedCh->chapter_number }}: {{ Str::limit($approvedCh->title, 25) }}
                    </option>
                @endforeach
            </select>

            <!-- Navigasi Prev/Next -->
            @if($prevChapter)
                <a href="{{ route('comics.chapters.show', [$comic->id, $prevChapter->chapter_number]) }}" class="btn btn-secondary btn-sm">
                    ← Prev
                </a>
            @else
                <span class="btn btn-secondary btn-sm opacity-40 pointer-events-none">← Prev</span>
            @endif

            @if($nextChapter)
                <a href="{{ route('comics.chapters.show', [$comic->id, $nextChapter->chapter_number]) }}" class="btn btn-secondary btn-sm">
                    Next →
                </a>
            @else
                <span class="btn btn-secondary btn-sm opacity-40 pointer-events-none">Next →</span>
            @endif
        </div>
    </div>

    <!-- Konten Halaman Komik atau Layar Terkunci (Premium) -->
    @if($isUnlocked)
        @if(is_array($chapter->pages) && count($chapter->pages) > 0)
            <div class="reader-pages">
                @foreach($chapter->pages as $index => $pagePath)
                    <img src="{{ asset('storage/' . $pagePath) }}" alt="Halaman {{ $index + 1 }}" class="comic-page-img" loading="lazy">
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;"><x-icon name="book-open" stroke-width="1.5" style="color: var(--text-muted)" /></div>
                <p style="font-size: 1.1rem; color: var(--text-primary);">Belum ada gambar halaman pada chapter ini.</p>
            </div>
        @endif
    @else
        <!-- Gerbang Kunci Chapter Premium (Simulasi Koin) -->
        <div class="card text-center py-16 px-8 my-8">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;"><x-icon name="lock" stroke-width="1.5" style="color: var(--text-muted)" /></div>
            <span class="badge badge-premium" style="font-size: 0.85rem; padding: 0.4rem 1rem; margin-bottom: 1rem;">
                Chapter Premium
            </span>
            <h2 style="font-size: 1.75rem; margin: 1rem 0 0.5rem 0; color: #fff;">
                Chapter Terkunci
            </h2>
            <p style="color: var(--text-secondary); max-width: 480px; margin: 0 auto 0.75rem auto; font-size: 0.95rem;">
                Chapter ini merupakan konten eksklusif dan membutuhkan 
                <strong style="color: #fbbf24;">{{ $chapter->coin_price }} Koin</strong> untuk dibuka.
            </p>
            <div style="margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
                Saldo Koin Anda saat ini: <strong style="color: #fbbf24;">{{ $userCoinBalance ?? 0 }} Koin</strong>
            </div>

            @if(($userCoinBalance ?? 0) >= $chapter->coin_price)
                <form action="{{ route('comics.chapters.unlock', [$comic->id, $chapter->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                        <x-icon name="coins" /> Buka Chapter dengan {{ $chapter->coin_price }} Koin (Simulasi)
                    </button>
                </form>
            @else
                <div class="alert alert-error max-w-md mx-auto mb-0 justify-center">
                    <x-icon name="triangle-alert" /> Saldo koin Anda tidak mencukupi (Kurang {{ $chapter->coin_price - ($userCoinBalance ?? 0) }} koin).
                </div>
            @endif
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 1rem;">
                * Sesuai ketentuan SKPL, sistem koin ini bersifat simulasi tanpa transaksi riil.
            </p>
        </div>
    @endif

    <!-- Bottom Navigation Bar -->
    <div class="card flex justify-center items-center flex-wrap gap-4 py-4">
        @if($prevChapter)
            <a href="{{ route('comics.chapters.show', [$comic->id, $prevChapter->chapter_number]) }}" class="btn btn-secondary btn-sm">
                ← Chapter Sebelumnya (Ch. {{ $prevChapter->chapter_number }})
            </a>
        @endif

        @if($isCreator || $isAdmin)
            <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-secondary btn-sm">
                <x-icon name="list" /> Daftar Chapter
            </a>
        @endif

        @if($nextChapter)
            <a href="{{ route('comics.chapters.show', [$comic->id, $nextChapter->chapter_number]) }}" class="btn btn-secondary btn-sm">
                Chapter Selanjutnya (Ch. {{ $nextChapter->chapter_number }}) →
            </a>
        @endif
    </div>
</div>
@endsection
