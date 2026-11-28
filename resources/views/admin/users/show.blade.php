@extends('layouts.app')

@section('title', 'Detail Akun - Admin KOMIQ')

@section('content')
<div class="admin-page-header">
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:1rem;">← Kembali ke Pengelolaan Akun</a>
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;"><img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:76px;height:76px;border-radius:9999px;border:2px solid var(--accent-primary);"><div><div style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;">Detail Akun</div><h1 style="font-size:2rem;margin-top:.15rem;">{{ $user->name }}</h1><p style="color:var(--text-secondary);margin-top:.15rem;">{{ $user->email }}</p></div></div>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;"><span class="badge badge-draft">{{ $user->role }}</span><span class="badge {{ $user->is_active ? 'badge-approved' : 'badge-rejected' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
</div>

<div class="admin-stat-grid">
    <div class="card admin-stat-card"><div class="admin-stat-label">Role Akun</div><div class="admin-stat-value" style="font-size:1.4rem;">{{ ucfirst($user->role) }}</div></div>
    <div class="card admin-stat-card" style="border-left:4px solid {{ $user->is_active ? '#10b981' : '#ef4444' }};"><div class="admin-stat-label" style="color:{{ $user->is_active ? 'var(--status-approved-text)' : 'var(--status-rejected-text)' }};">Status Akun</div><div class="admin-stat-value" style="font-size:1.4rem;">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
    <div class="card admin-stat-card"><div class="admin-stat-label">{{ $user->isKreator() ? 'Komik Dikelola' : 'Komentar Dibuat' }}</div><div class="admin-stat-value">{{ $user->isKreator() ? $user->comics->count() : $user->comments->count() }}</div></div>
    <div class="card admin-stat-card"><div class="admin-stat-label">Terdaftar Sejak</div><div class="admin-stat-value" style="font-size:1.15rem;">{{ $user->created_at?->format('d M Y') }}</div></div>
</div>

@if($user->isKreator())
    <div class="card admin-table-card"><div class="admin-table-title"><h2 style="font-size:1.15rem;margin:0;">Komik Milik Kreator</h2><span style="font-size:.85rem;color:var(--text-muted);">{{ $user->comics->count() }} komik</span></div>@forelse($user->comics as $comic)<div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;gap:1rem;"><div><div style="font-weight:600;color:var(--text-primary);">{{ $comic->title }}</div><div style="font-size:.8rem;color:var(--text-muted);margin-top:.2rem;">{{ $comic->chapters->count() }} chapter · {{ ucfirst($comic->status) }}</div></div><a class="btn btn-secondary btn-sm" href="{{ route('admin.comics.show',$comic) }}">Detail Moderasi</a></div>@empty<div style="padding:3rem 1.5rem;text-align:center;color:var(--text-muted);">Kreator ini belum memiliki komik.</div>@endforelse</div>
@else
    <div class="card"><h2 style="font-size:1.15rem;margin:0 0 .5rem;">Ringkasan Aktivitas</h2><p style="color:var(--text-secondary);margin:0;">@if($user->isAdmin()) Akun ini berperan sebagai Administrator dan memiliki akses pengelolaan platform. @elseif($user->comments->isEmpty()) Pembaca ini belum memberikan komentar. @else Pembaca ini telah memberikan {{ $user->comments->count() }} komentar. @endif</p></div>
@endif

<div class="card admin-table-card"><div class="admin-table-title"><h2 style="font-size:1.15rem;margin:0;">Komentar Terbaru</h2><span style="font-size:.85rem;color:var(--text-muted);">{{ $user->comments->count() }} komentar</span></div>@forelse($user->comments->take(10) as $comment)<div style="padding:1rem 1.5rem;border-bottom:1px solid var(--border-color);"><div style="color:var(--text-primary);">{{ $comment->content }}</div><div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;">{{ $comment->chapter?->comic?->title ?? 'Komik tidak tersedia' }} · {{ $comment->created_at?->format('d M Y, H:i') }}</div></div>@empty<div style="padding:3rem 1.5rem;text-align:center;color:var(--text-muted);">Belum ada komentar dari akun ini.</div>@endforelse</div>
@endsection
