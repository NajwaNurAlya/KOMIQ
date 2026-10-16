@extends('layouts.app')

@section('title', 'Membaca ' . ($comic->title ?? 'Komik') . ' - Chapter ' . $chapter->chapter_number)

@section('styles')
<style>
    .reader-container {
        max-width: 820px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .reader-header {
        background: rgba(19, 27, 46, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
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

    .chapter-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        background: var(--bg-card-hover);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        transition: all 0.2s;
    }

    .chapter-nav-btn:hover:not(.disabled) {
        background: var(--accent-primary);
        color: #fff;
    }

    .chapter-nav-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    .lock-screen {
        background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        margin: 2rem 0;
        box-shadow: 0 20px 30px -10px rgba(0,0,0,0.5);
    }
</style>
@endsection

@section('content')
<div class="reader-container">
    @if(! $chapter->isApproved())
        <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
            <span>👁️</span>
            <div>
                <strong>Mode Pratinjau Kreator / Admin:</strong> Chapter ini saat ini berstatus 
                <span class="badge badge-{{ $chapter->status }}" style="margin: 0 4px;">{{ strtoupper($chapter->status) }}</span> 
                dan belum dapat diakses oleh publik luas.
            </div>
        </div>
    @endif

    <!-- Top Navigation Bar -->
    <div class="reader-header">
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
                <a href="{{ route('comics.chapters.show', [$comic->id, $prevChapter->chapter_number]) }}" class="chapter-nav-btn">
                    ← Prev
                </a>
            @else
                <span class="chapter-nav-btn disabled">← Prev</span>
            @endif

            @if($nextChapter)
                <a href="{{ route('comics.chapters.show', [$comic->id, $nextChapter->chapter_number]) }}" class="chapter-nav-btn">
                    Next →
                </a>
            @else
                <span class="chapter-nav-btn disabled">Next →</span>
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
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">📖</div>
                <p style="font-size: 1.1rem; color: var(--text-primary);">Belum ada gambar halaman pada chapter ini.</p>
            </div>
        @endif
    @else
        <!-- Gerbang Kunci Chapter Premium (Simulasi Koin) -->
        <div class="lock-screen">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">🔒</div>
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
                Saldo Koin Anda saat ini: <strong style="color: #60a5fa;">{{ $userCoinBalance ?? 0 }} Koin</strong>
            </div>

            @if(($userCoinBalance ?? 0) >= $chapter->coin_price)
                <form action="{{ route('comics.chapters.unlock', [$comic->id, $chapter->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                        🪙 Buka Chapter dengan {{ $chapter->coin_price }} Koin (Simulasi)
                    </button>
                </form>
            @else
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 0.75rem 1.25rem; max-width: 400px; margin: 0 auto; color: #fca5a5; font-size: 0.9rem;">
                    ⚠️ Saldo koin Anda tidak mencukupi (Kurang {{ $chapter->coin_price - ($userCoinBalance ?? 0) }} koin).
                </div>
            @endif
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 1rem;">
                * Sesuai ketentuan SKPL, sistem koin ini bersifat simulasi tanpa transaksi riil.
            </p>
        </div>
    @endif

    <!-- Bottom Navigation Bar -->
    <div class="reader-header" style="justify-content: center; gap: 1rem;">
        @if($prevChapter)
            <a href="{{ route('comics.chapters.show', [$comic->id, $prevChapter->chapter_number]) }}" class="chapter-nav-btn">
                ← Chapter Sebelumnya (Ch. {{ $prevChapter->chapter_number }})
            </a>
        @endif

        <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="chapter-nav-btn">
            📚 Daftar Chapter
        </a>

        @if($nextChapter)
            <a href="{{ route('comics.chapters.show', [$comic->id, $nextChapter->chapter_number]) }}" class="chapter-nav-btn">
                Chapter Selanjutnya (Ch. {{ $nextChapter->chapter_number }}) →
            </a>
        @endif
    </div>
</div>
@endsection
