@extends('layouts.app')

@section('title', 'Manajemen Genre - Admin KOMIQ')

@section('styles')
<style>
    .genre-actions { display:flex; gap:.5rem; align-items:center; }
    .genre-dialog { width:min(92vw, 480px); border:1px solid var(--border-color); border-radius:16px; padding:0; background:var(--bg-card); color:var(--text-primary); box-shadow:0 24px 70px rgba(0,0,0,.6); }
    .genre-dialog::backdrop { background:rgba(0,0,0,.7); }
    .genre-dialog-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); }
    .genre-dialog-body { padding:1.5rem; }
    @media (max-width:640px) { .genre-actions { flex-wrap:wrap; } .genre-name-input { min-width:150px; } }
</style>
@endsection

@section('content')
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm" style="margin-bottom:1.5rem;">← Kembali ke Dashboard Admin</a>

<div class="admin-page-header">
    <div>
        <div style="font-size:.875rem;color:var(--text-muted);margin-bottom:.25rem;">Panel Administrasi</div>
        <h1 style="font-size:2rem;">Manajemen Genre</h1>
        <p style="color:var(--text-secondary);margin-top:.25rem;">Kelola kategori komik tanpa menghilangkan relasi genre yang sudah dipakai.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('create-genre').showModal()"><x-icon name="plus" /> Tambah Genre</button>
    </div>
</div>

<section class="card admin-table-card">
    <div class="admin-table-title">
        <div><h2 style="font-size:1.15rem;margin:0;">Daftar Genre</h2><p style="font-size:.8rem;color:var(--text-muted);margin:.25rem 0 0;">Ubah nama langsung di tabel atau hapus melalui tindakan terpisah.</p></div>
        <span style="font-size:.85rem;color:var(--text-muted);">{{ $genres->total() }} genre</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <colgroup><col><col><col><col style="width:180px;"></colgroup>
            <thead><tr><th>Nama Genre</th><th>Slug</th><th>Digunakan</th><th style="width:180px;">Aksi</th></tr></thead>
            <tbody>
                @forelse($genres as $genre)
                    <tr>
                        <td><form id="genre-{{ $genre->id }}" method="POST" action="{{ route('admin.genres.update', $genre) }}" onsubmit="return confirm('Simpan perubahan nama genre ini?');">@csrf @method('PUT')<input class="form-control genre-name-input" style="min-width:180px;" name="name" value="{{ $genre->name }}" maxlength="50" required></form></td>
                        <td style="color:var(--text-muted);">{{ $genre->slug }}</td>
                        <td style="color:var(--text-secondary);">{{ $genre->comics_count }} komik</td>
                        <td style="width:180px;"><div class="genre-actions"><button class="btn btn-secondary btn-sm" form="genre-{{ $genre->id }}">Simpan</button><button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('delete-genre-{{ $genre->id }}').showModal()">Hapus</button></div></td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:3rem;text-align:center;color:var(--text-muted);">Belum ada genre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--border-color);">{{ $genres->links() }}</div>
</section>

<dialog id="create-genre" class="genre-dialog" @if($errors->has('name')) open @endif>
    <div class="genre-dialog-header"><h2 style="font-size:1.2rem;margin:0;">Tambah Genre</h2></div>
    <form method="POST" action="{{ route('admin.genres.store') }}" class="genre-dialog-body" onsubmit="return confirm('Tambahkan genre baru ini?');">
        @csrf
        <p style="font-size:.9rem;color:var(--text-secondary);margin-bottom:1.25rem;">Genre baru akan tersedia saat Kreator membuat atau mengubah komik.</p>
        <div class="form-group"><label class="form-label" for="name">Nama Genre</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" maxlength="50" required autofocus placeholder="Contoh: Action">@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div style="display:flex;justify-content:flex-end;gap:.75rem;"><button type="button" class="btn btn-secondary" onclick="document.getElementById('create-genre').close()">Batal</button><button class="btn btn-primary">Tambah Genre</button></div>
    </form>
</dialog>

@foreach($genres as $genre)
    <dialog id="delete-genre-{{ $genre->id }}" class="genre-dialog">
        <div class="genre-dialog-header"><h2 style="font-size:1.2rem;margin:0;">Hapus Genre “{{ $genre->name }}”?</h2></div>
        <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}" class="genre-dialog-body" onsubmit="return confirm('Yakin ingin menghapus genre ini? Tindakan ini tidak dapat dibatalkan.');">
            @csrf @method('DELETE')
            @if($genre->comics_count)
                <p style="color:var(--text-secondary);font-size:.9rem;margin-bottom:1rem;">Genre ini dipakai oleh <strong style="color:#fff;">{{ $genre->comics_count }} komik</strong>. Pilih genre pengganti agar seluruh komik tetap memiliki kategori.</p>
                <div class="form-group"><label class="form-label">Genre Pengganti</label><select name="replacement_genre_id" class="form-control" required><option value="">Pilih genre pengganti</option>@foreach($allGenres->where('id','!=',$genre->id) as $replacement)<option value="{{ $replacement->id }}">{{ $replacement->name }}</option>@endforeach</select></div>
            @else
                <p style="color:var(--text-secondary);font-size:.9rem;margin-bottom:1rem;">Genre ini belum dipakai komik dan dapat dihapus dengan aman.</p>
            @endif
            <div style="display:flex;justify-content:flex-end;gap:.75rem;"><button type="button" class="btn btn-secondary" onclick="document.getElementById('delete-genre-{{ $genre->id }}').close()">Batal</button><button class="btn btn-danger">Hapus Genre</button></div>
        </form>
    </dialog>
@endforeach
@endsection
