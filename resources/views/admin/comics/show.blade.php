@extends('layouts.app')

@section('title', 'Detail Komik - Admin')

@section('content')
<a href="{{ route('admin.comics.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 1.5rem;">← Kembali ke Manajemen Komik</a>

<div class="admin-page-header">
    <div>
        <div style="font-size: .875rem; color: var(--text-muted); margin-bottom: .25rem;">Karya oleh {{ $comic->user?->name ?? 'Kreator' }}</div>
        <h1 style="font-size: 2rem;">{{ $comic->title }}</h1>
        <p style="color: var(--text-secondary); margin-top: .25rem;">Status publikasi: {{ $comic->status }}</p>
    </div>
    <span class="badge {{ $comic->is_hidden ? 'badge-rejected' : 'badge-approved' }}">{{ $comic->is_hidden ? 'Disembunyikan' : 'Tampil untuk publik' }}</span>
</div>

<div class="admin-stat-grid">
    <div class="card admin-stat-card"><div class="admin-stat-label">Kreator</div><div style="margin-top:.4rem; font-weight:700; color:#fff;">{{ $comic->user?->name ?? '-' }}</div></div>
    <div class="card admin-stat-card"><div class="admin-stat-label">Genre</div><div style="margin-top:.4rem; font-weight:700; color:#fff;">{{ $comic->genres->count() }} genre</div></div>
    <div class="card admin-stat-card"><div class="admin-stat-label">Chapter</div><div class="admin-stat-value">{{ $comic->chapters->count() }}</div></div>
</div>

<div class="card">
    <h2 style="font-size:1.15rem; margin-bottom:.75rem;">Sinopsis</h2>
    <p class="whitespace-pre-line" style="color:var(--text-secondary);">{{ $comic->synopsis ?: 'Tidak ada sinopsis.' }}</p>
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color); color:var(--text-secondary);"><span style="color:var(--text-muted);">Genre:</span> {{ $comic->genres->pluck('name')->join(', ') ?: '-' }}</div>
</div>

<div class="card">
    <h2 style="font-size:1.15rem; margin-bottom:.4rem;">Tindakan Moderasi</h2>
    <p style="font-size:.875rem; color:var(--text-muted); margin-bottom:1rem;">Tindakan ini tidak menghapus komik ataupun chapter milik kreator.</p>
    <form method="POST" action="{{ $comic->is_hidden ? route('admin.comics.unhide', $comic) : route('admin.comics.hide', $comic) }}" onsubmit="return confirm('{{ $comic->is_hidden ? 'Tampilkan kembali komik ini untuk publik?' : 'Sembunyikan komik ini dari pembaca publik?' }}');">
        @csrf
        @method('PATCH')
        <button class="btn {{ $comic->is_hidden ? 'btn-success' : 'btn-danger' }}">{{ $comic->is_hidden ? 'Tampilkan kembali' : 'Sembunyikan dari publik' }}</button>
    </form>
</div>
@endsection
