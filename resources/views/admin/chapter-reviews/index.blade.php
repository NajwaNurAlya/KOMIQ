@extends('layouts.app')

@section('title', 'Moderasi Chapter - Panel Admin KOMIQ')

@section('content')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Panel Administrasi</div>
    <h1 style="font-size: 2rem;"><x-icon name="shield" /> Moderasi & Review Chapter</h1>
    <p style="color: var(--text-secondary); margin-top: 0.25rem;">
        Tinjau chapter komik yang dikirimkan oleh para kreator. Setujui untuk dipublikasikan atau tolak dengan catatan perbaikan.
    </p>
</div>

<!-- Tab Filter Status -->
<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'pending']) }}" 
       class="btn {{ $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        <x-icon name="hourglass" /> Menunggu Review ({{ $counts['pending'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'approved']) }}" 
       class="btn {{ $statusFilter === 'approved' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        <x-icon name="circle-check" /> Disetujui ({{ $counts['approved'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'rejected']) }}" 
       class="btn {{ $statusFilter === 'rejected' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        <x-icon name="circle-x" /> Ditolak ({{ $counts['rejected'] }})
    </a>
    <a href="{{ route('admin.chapter-reviews.index', ['status' => 'all']) }}" 
       class="btn {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm" style="border-radius: 9999px;">
        <x-icon name="clipboard-list" /> Semua Chapter ({{ $counts['all'] }})
    </a>
</div>

<!-- Tabel Antrean Moderasi -->
<div class="card" style="padding: 0; overflow: hidden;">
    @if($chapters->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Komik & Kreator</th>
                        <th>Chapter</th>
                        <th>Halaman</th>
                        <th>Status</th>
                        <th>Waktu Pengajuan</th>
                        <th style="text-align: right;">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chapters as $chapter)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #fff;">{{ $chapter->comic?->title ?? 'Komik #' . $chapter->comic_id }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    Oleh: <strong>{{ $chapter->comic?->user?->name ?? 'Kreator' }}</strong>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">
                                    Ch. {{ $chapter->chapter_number }}: {{ $chapter->title }}
                                </div>
                                <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                                    @if($chapter->is_premium)
                                        <span class="badge badge-premium" style="font-size: 0.7rem;"><x-icon name="coins" /> {{ $chapter->coin_price }} Koin</span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">Gratis</span>
                                    @endif
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">
                                <x-icon name="image" /> {{ $chapter->page_count }} Hal
                            </td>
                            <td>
                                <x-chapter-status :chapter="$chapter" />
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $chapter->updated_at?->diffForHumans() }}
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.chapter-reviews.show', $chapter->id) }}" class="btn btn-primary btn-sm">
                                    <x-icon name="search" /> Periksa & Review
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
            <div style="font-size: 3rem; margin-bottom: 0.5rem;"><x-icon name="inbox" stroke-width="1.5" style="color: var(--text-muted)" /></div>
            <p style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.25rem;">Tidak ada chapter dalam kategori ini.</p>
            <p style="font-size: 0.875rem;">Semua chapter yang dikirimkan telah diproses atau belum ada pengajuan baru.</p>
        </div>
    @endif
</div>
@endsection
