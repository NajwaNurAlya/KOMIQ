@extends('layouts.app')

@section('title', 'Kelola Chapter - ' . ($comic->title ?? 'Komik'))

@section('content')
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Manajemen Komik Kreator</div>
        <h1 style="font-size: 2rem;">{{ $comic->title ?? 'Komik #' . $comic->id }}</h1>
        <p style="color: var(--text-secondary); margin-top: 0.25rem;">Kelola seluruh chapter, pantau status peninjauan admin, dan publikasikan karya Anda.</p>
    </div>
    <div>
        <a href="{{ route('creator.comics.chapters.create', $comic->id) }}" class="btn btn-primary">
            <x-icon name="plus" /> Tambah Chapter Baru
        </a>
    </div>
</div>

<!-- Statistik Status Chapter -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0; padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Total Chapter</div>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.25rem;">{{ $counts['all'] }}</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #64748b;">
        <div style="font-size: 0.8rem; color: var(--status-draft-text); text-transform: uppercase;">Draft</div>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.25rem;">{{ $counts['draft'] }}</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #f59e0b;">
        <div style="font-size: 0.8rem; color: var(--status-pending-text); text-transform: uppercase;">Menunggu Review</div>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.25rem;">{{ $counts['pending'] }}</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #10b981;">
        <div style="font-size: 0.8rem; color: var(--status-approved-text); text-transform: uppercase;">Disetujui (Publik)</div>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.25rem;">{{ $counts['approved'] }}</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1.25rem; border-left: 4px solid #ef4444;">
        <div style="font-size: 0.8rem; color: var(--status-rejected-text); text-transform: uppercase;">Ditolak</div>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.25rem;">{{ $counts['rejected'] }}</div>
    </div>
</div>

<!-- Daftar Chapter -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.15rem; margin: 0;">Daftar Chapter Komik</h3>
    </div>

    @if($chapters->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Chapter</th>
                        <th>Halaman</th>
                        <th>Tipe & Harga</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chapters as $chapter)
                        <tr>
                            <td style="font-weight: 700; color: #fff;">
                                Ch. {{ $chapter->chapter_number }}
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $chapter->title }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    Dibuat: {{ $chapter->created_at?->format('d M Y, H:i') }}
                                </div>

                                @if($chapter->isRejected() && $chapter->review_note)
                                    <div style="margin-top: 0.75rem; padding: 0.6rem 0.9rem; background: rgba(239, 68, 68, 0.1); border-left: 3px solid #ef4444; border-radius: 6px; font-size: 0.825rem; color: #fca5a5;">
                                        <strong><x-icon name="triangle-alert" /> Catatan Penolakan Admin:</strong> {{ $chapter->review_note }}
                                    </div>
                                @endif
                            </td>
                            <td style="color: var(--text-secondary);">
                                <x-icon name="file-text" /> {{ $chapter->page_count }} Hal
                            </td>
                            <td>
                                @if($chapter->is_premium)
                                    <span class="badge badge-premium"><x-icon name="coins" /> {{ $chapter->coin_price }} Koin</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">Gratis</span>
                                @endif
                            </td>
                            <td>
                                <x-chapter-status :chapter="$chapter" />
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                    @if($chapter->canBeSubmitted())
                                        <form action="{{ route('creator.comics.chapters.submit', [$comic->id, $chapter->id]) }}" method="POST" onsubmit="return confirm('Kirim chapter ini ke Admin untuk ditinjau?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Kirim untuk ditinjau Admin">
                                                <x-icon name="send" /> Ajukan
                                            </button>
                                        </form>
                                    @endif

                                    @if($chapter->canBeEdited())
                                        <a href="{{ route('creator.comics.chapters.edit', [$comic->id, $chapter->id]) }}" class="btn btn-secondary btn-sm">
                                            <x-icon name="pencil" /> Edit
                                        </a>
                                        <form action="{{ route('creator.comics.chapters.destroy', [$comic->id, $chapter->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus chapter ini? Tindakan tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <x-icon name="trash-2" />
                                            </button>
                                        </form>
                                    @endif

                                    @if($chapter->isApproved())
                                        <a href="{{ route('comics.chapters.show', [$comic->id, $chapter->chapter_number]) }}" target="_blank" class="btn btn-secondary btn-sm" title="Baca di Webtoon Reader">
                                            <x-icon name="book-open" /> Baca Publik
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
            {{ $chapters->links() }}
        </div>
    @else
        <div style="padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;"><x-icon name="book-open" stroke-width="1.5" style="color: var(--text-muted)" /></div>
            <p style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.5rem;">Belum ada chapter pada komik ini.</p>
            <p style="font-size: 0.9rem; margin-bottom: 1.5rem;">Mulai dengan mengunggah chapter perdana untuk komik Anda.</p>
            <a href="{{ route('creator.comics.chapters.create', $comic->id) }}" class="btn btn-primary">
                <x-icon name="plus" /> Unggah Chapter Perdana
            </a>
        </div>
    @endif
</div>
@endsection
