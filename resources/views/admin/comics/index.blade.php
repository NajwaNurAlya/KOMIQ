@extends('layouts.app')

@section('title', 'Manajemen Komik - Admin')

@section('content')
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 1.5rem;">← Kembali ke Dashboard Admin</a>

<div class="admin-page-header">
    <div>
        <div style="font-size: .875rem; color: var(--text-muted); margin-bottom: .25rem;">Panel Administrasi</div>
        <h1 style="font-size: 2rem;">Manajemen Komik</h1>
        <p style="color: var(--text-secondary); margin-top: .25rem;">Pantau visibilitas konten tanpa menghapus karya maupun chapter kreator.</p>
    </div>
</div>

<div style="display: flex; gap: .75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    @foreach(['all' => 'Semua', 'visible' => 'Tampil', 'hidden' => 'Disembunyikan'] as $key => $label)
        <a class="btn btn-sm {{ $visibility === $key ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.comics.index', ['visibility' => $key]) }}">
            {{ $label }} ({{ $counts[$key] }})
        </a>
    @endforeach
</div>

<div class="card admin-table-card">
    <div class="admin-table-title">
        <h2 style="font-size: 1.15rem;">Daftar Komik</h2>
        <span style="font-size: .85rem; color: var(--text-muted);">{{ $comics->total() }} komik</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead><tr><th>Komik</th><th>Kreator</th><th>Chapter</th><th>Visibilitas</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($comics as $comic)
                    <tr>
                        <td style="font-weight: 700; color: #fff;">{{ $comic->title }}</td>
                        <td>{{ $comic->user?->name ?? '-' }}</td>
                        <td>{{ $comic->chapters_count }} chapter</td>
                        <td><span class="badge {{ $comic->is_hidden ? 'badge-rejected' : 'badge-approved' }}">{{ $comic->is_hidden ? 'Disembunyikan' : 'Tampil' }}</span></td>
                        <td><a class="btn btn-secondary btn-sm" href="{{ route('admin.comics.show', $comic) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:3rem 1.25rem;">Tidak ada komik pada kategori ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">{{ $comics->links() }}</div>
</div>
@endsection
