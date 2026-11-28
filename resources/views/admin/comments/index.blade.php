@extends('layouts.app')

@section('title', 'Moderasi Komentar - Admin')

@section('content')
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm" style="margin-bottom:1.5rem;">← Kembali ke Dashboard Admin</a>
<div class="admin-page-header"><div><div style="font-size:.875rem;color:var(--text-muted);margin-bottom:.25rem;">Panel Administrasi</div><h1 style="font-size:2rem;">Moderasi Komentar</h1><p style="color:var(--text-secondary);margin-top:.25rem;">Tinjau hanya komentar yang dilaporkan Pembaca, lalu pertahankan atau sembunyikan tanpa menghapus data.</p></div></div>

<div style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">@foreach(['pending'=>'Perlu Ditinjau', 'visible'=>'Sudah Ditinjau', 'hidden'=>'Disembunyikan', 'all'=>'Semua'] as $key=>$label)<a class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.comments.index', ['status'=>$key]) }}">{{ $label }} ({{ $counts[$key] }})</a>@endforeach</div>

<div class="card admin-table-card"><div class="admin-table-title"><div><h2 style="font-size:1.15rem;margin:0;">{{ $status === 'pending' ? 'Antrean Laporan Komentar' : 'Daftar Komentar' }}</h2><p style="font-size:.8rem;color:var(--text-muted);margin:.25rem 0 0;">{{ $status === 'pending' ? 'Hanya komentar dengan laporan aktif yang tampil di antrean ini.' : 'Gunakan filter untuk melihat status moderasi komentar.' }}</p></div><span style="font-size:.85rem;color:var(--text-muted);">{{ $comments->total() }} komentar</span></div>
    <div style="overflow-x:auto;"><table class="table"><thead><tr><th>Komentar</th><th>Pengirim</th><th>Komik / Chapter</th><th style="min-width:150px;">Status</th><th>Aksi</th></tr></thead><tbody>
        @forelse($comments as $comment)
            <tr>
                <td style="min-width:260px;color:var(--text-secondary);">{{ $comment->content }}@if($comment->reports->isNotEmpty())<div style="font-size:.78rem;color:var(--status-pending-text);margin-top:.5rem;">{{ $comment->reports->count() }} laporan · {{ $comment->reports->pluck('reason')->unique()->join(', ') }}</div>@endif</td>
                <td><div style="font-weight:600;color:#fff;">{{ $comment->user?->name ?? '-' }}</div><div style="font-size:.8rem;color:var(--text-muted);">{{ $comment->user?->email }}</div></td>
                <td><div style="font-weight:600;color:#fff;">{{ $comment->chapter?->comic?->title ?? '-' }}</div><div style="font-size:.8rem;color:var(--text-muted);">Ch. {{ $comment->chapter?->chapter_number ?? '-' }}</div></td>
                <td style="min-width:150px;">@if($comment->is_hidden)<span class="badge badge-rejected">Disembunyikan</span>@elseif($comment->reports->isNotEmpty())<span class="badge badge-pending">{{ $comment->reports->count() }} Laporan</span>@else<span class="badge badge-approved">Tampil</span>@endif</td>
                <td style="white-space:nowrap;"><div style="display:inline-flex;gap:.5rem;">@if(! $comment->is_hidden && $comment->reports->isNotEmpty())<form method="POST" action="{{ route('admin.comments.review', $comment) }}" onsubmit="return confirm('Tutup laporan dan pertahankan komentar ini?');">@csrf @method('PATCH')<button class="btn btn-secondary btn-sm">Pertahankan</button></form>@endif<form method="POST" action="{{ $comment->is_hidden ? route('admin.comments.unhide', $comment) : route('admin.comments.hide', $comment) }}" onsubmit="return confirm('{{ $comment->is_hidden ? 'Pulihkan komentar ini agar tampil kembali?' : 'Sembunyikan komentar ini dari publik?' }}');">@csrf @method('PATCH')<button class="btn btn-sm {{ $comment->is_hidden ? 'btn-success' : 'btn-danger' }}">{{ $comment->is_hidden ? 'Pulihkan' : 'Sembunyikan' }}</button></form></div></td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:3rem 1.25rem;">Tidak ada komentar pada kategori ini.</td></tr>
        @endforelse
    </tbody></table></div>
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border-color);">{{ $comments->links() }}</div>
</div>
@endsection
