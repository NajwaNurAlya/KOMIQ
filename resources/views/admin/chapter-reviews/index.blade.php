@extends('layouts.app')

@section('title', 'Moderasi Chapter - Panel Admin KOMIQ')

@section('content')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Panel Administrasi</div>
    <h1 style="font-size: 2rem;">🛡️ Moderasi & Review Chapter</h1>
    <p style="color: var(--text-secondary); margin-top: 0.25rem;">
        Tinjau chapter komik yang dikirimkan oleh para kreator. Setujui untuk dipublikasikan atau tolak dengan catatan perbaikan.
    </p>
</div>

<!-- Tab Filter Status -->
<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'pending']) }}" 
       class="btn {{ $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        ⏳ Menunggu Review ({{ $counts['pending'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'approved']) }}" 
       class="btn {{ $statusFilter === 'approved' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        ✅ Disetujui ({{ $counts['approved'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'rejected']) }}" 
       class="btn {{ $statusFilter === 'rejected' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        ❌ Ditolak ({{ $counts['rejected'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'all']) }}" 
       class="btn {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        📋 Semua Chapter ({{ $counts['all'] }})
    </a>
</div>

<!-- Tabel Antrean Moderasi -->
<div class="card" style="padding: 0; overflow: hidden;">
    @if($chapters->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.925rem;">
                <thead>
                    <tr style="background: rgba(19, 27, 46, 0.6); color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 1rem 1.25rem;">Komik & Kreator</th>
                        <th style="padding: 1rem 1.25rem;">Chapter</th>
                        <th style="padding: 1rem 1.25rem;">Halaman</th>
                        <th style="padding: 1rem 1.25rem;">Status</th>
                        <th style="padding: 1rem 1.25rem;">Waktu Pengajuan</th>
                        <th style="padding: 1rem 1.25rem; text-align: right;">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chapters as $chapter)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;">
                            <td style="padding: 1.25rem;">
                                <div style="font-weight: 700; color: #fff;">{{ $chapter->comic?->title ?? 'Komik #' . $chapter->comic_id }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    Oleh: <strong>{{ $chapter->comic?->user?->name ?? 'Kreator' }}</strong>
                                </div>
                            </td>
                            <td style="padding: 1.25rem;">
                                <div style="font-weight: 600; color: var(--text-primary);">
                                    Ch. {{ $chapter->chapter_number }}: {{ $chapter->title }}
                                </div>
                                <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                                    @if($chapter->is_premium)
                                        <span class="badge badge-premium" style="font-size: 0.7rem;">🪙 {{ $chapter->coin_price }} Koin</span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">Gratis</span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 1.25rem; color: var(--text-secondary);">
                                🖼️ {{ $chapter->page_count }} Hal
                            </td>
                            <td style="padding: 1.25rem;">
                                @if($chapter->isDraft())
                                    <span class="badge badge-draft">Draft</span>
                                @elseif($chapter->isPending())
                                    <span class="badge badge-pending">Menunggu Review</span>
                                @elseif($chapter->isApproved())
                                    <span class="badge badge-approved">Approved</span>
                                @elseif($chapter->isRejected())
                                    <span class="badge badge-rejected">Rejected</span>
                                @endif
                            </td>
                            <td style="padding: 1.25rem; font-size: 0.85rem; color: var(--text-muted);">
                                {{ $chapter->updated_at?->diffForHumans() }}
                            </td>
                            <td style="padding: 1.25rem; text-align: right;">
                                <a href="{{ route('admin.chapter-reviews.show', $chapter->id) }}" class="btn btn-primary btn-sm">
                                    🔍 Periksa & Review
                                </a>
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
        <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
            <p style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.25rem;">Tidak ada chapter dalam kategori ini.</p>
            <p style="font-size: 0.875rem;">Semua chapter yang dikirimkan telah diproses atau belum ada pengajuan baru.</p>
        </div>
    @endif
</div>
@endsection
