@extends('layouts.app')

@section('title', 'Komik Saya - Kreator Hub')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Kreator Hub</div>
        <h1 style="font-size: 2rem;">Komik Saya</h1>
        <p style="color: var(--text-secondary); margin-top: 0.25rem;">
            Kelola seluruh karya komik serial Anda, pantau jumlah chapter, dan perbarui identitas komik.
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('creator.hub') }}" class="btn btn-secondary">
            <x-icon name="house" /> Kreator Hub
        </a>
        <a href="{{ route('creator.comics.create') }}" class="btn btn-primary">
            <x-icon name="plus" /> Buat Komik Baru
        </a>
    </div>
</div>

{{-- Filter & Pencarian --}}
<div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
    <form action="{{ route('creator.comics.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px; position: relative;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul komik atau sinopsis..." class="form-control" style="padding-left: 2.25rem;">
            <span style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">
                <x-icon name="search" />
            </span>
        </div>
        <div style="min-width: 150px;">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Berjalan (Ongoing)</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Tamat (Completed)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;">
            Filter
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('creator.comics.index') }}" class="btn btn-secondary btn-sm" style="height: 38px;" title="Reset Filter">
                Reset
            </a>
        @endif
    </form>
</div>

@if($comics->isEmpty())
    <div class="card" style="text-align: center; padding: 4rem 1.5rem;">
        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;">
            <x-icon name="book-open" size="3rem" />
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #fff;">
            @if(request()->hasAny(['search', 'status']))
                Tidak ada komik yang cocok dengan kriteria pencarian
            @else
                Belum Ada Komik yang Dibuat
            @endif
        </h3>
        <p style="color: var(--text-muted); max-width: 450px; margin: 0 auto 1.5rem auto; font-size: 0.9rem;">
            @if(request()->hasAny(['search', 'status']))
                Coba gunakan kata kunci pencarian lain atau hilangkan filter status komik.
            @else
                Mulailah mempublikasikan karya komik pertama Anda untuk menjangkau para pembaca di KOMIQ!
            @endif
        </p>
        <div>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('creator.comics.index') }}" class="btn btn-secondary">
                    Reset Filter
                </a>
            @else
                <a href="{{ route('creator.comics.create') }}" class="btn btn-primary">
                    <x-icon name="plus" /> Buat Komik Sekarang
                </a>
            @endif
        </div>
    </div>
@else
    {{-- Grid Kartu Komik --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @foreach($comics as $comic)
            <div class="card" style="margin-bottom: 0; padding: 0; display: flex; flex-direction: column; overflow: hidden; border-radius: 14px; transition: transform 0.2s, border-color 0.2s; position: relative;"
                 onmouseover="this.style.borderColor='#52525b'"
                 onmouseout="this.style.borderColor='var(--border-color)'">
                
                {{-- Bagian Cover Sampul --}}
                <div style="position: relative; width: 100%; aspect-ratio: 16/10; background-color: var(--bg-secondary); overflow: hidden;">
                    @if($comic->cover_image)
                        <img src="{{ asset('storage/' . $comic->cover_image) }}" alt="{{ $comic->title }}"
                             style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); background: linear-gradient(135deg, #181920 0%, #27272a 100%);">
                            <x-icon name="image" size="2.5rem" />
                            <span style="font-size: 0.75rem; margin-top: 0.5rem; font-weight: 500;">Tanpa Sampul</span>
                        </div>
                    @endif

                    {{-- Badge Status di pojok kanan atas sampul --}}
                    <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                        @if($comic->status === 'completed')
                            <span class="badge badge-approved" style="box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
                                <x-icon name="circle-check" /> Tamat
                            </span>
                        @else
                            <span class="badge badge-pending" style="box-shadow: 0 2px 6px rgba(0,0,0,0.4);">
                                <x-icon name="hourglass" /> Berjalan
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Konten Informasi Komik --}}
                <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                    {{-- Daftar Genre --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.65rem;">
                        @forelse($comic->genres as $genre)
                            <span style="font-size: 0.7rem; padding: 0.15rem 0.5rem; background: rgba(220, 38, 38, 0.12); color: var(--accent-text); border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 4px; font-weight: 600;">
                                {{ $genre->name }}
                            </span>
                        @empty
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Tanpa Genre</span>
                        @endforelse
                    </div>

                    {{-- Judul Komik --}}
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 0.4rem; line-height: 1.35; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="{{ $comic->title }}">
                        {{ $comic->title }}
                    </h3>

                    {{-- Sinopsis Komik --}}
                    <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; flex: 1;">
                        {{ $comic->synopsis ?: 'Belum ada sinopsis untuk serial ini.' }}
                    </p>

                    {{-- Statistik Chapter --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--border-color); font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--text-primary); font-weight: 600;">
                            <x-icon name="book-open" /> {{ $comic->chapters_count ?? 0 }} Chapter
                        </span>
                        <span>
                            Diperbarui: {{ $comic->updated_at?->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Tombol Aksi: Kelola Chapter, Edit, Hapus --}}
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: auto;">
                        <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-primary btn-sm" style="flex: 1;" title="Buka manajemen chapter komik ini">
                            <x-icon name="list" /> Kelola Chapter
                        </a>
                        <a href="{{ route('creator.comics.edit', $comic->id) }}" class="btn btn-secondary btn-sm" title="Edit identitas & sampul komik">
                            <x-icon name="pencil" /> Edit
                        </a>
                        <form action="{{ route('creator.comics.destroy', $comic->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus komik \'{{ addslashes($comic->title) }}\'? Seluruh data chapter di dalamnya akan terhapus secara permanen.');"
                              style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus komik">
                                <x-icon name="trash-2" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Navigasi Paginasi --}}
    @if($comics->hasPages())
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $comics->links() }}
        </div>
    @endif
@endif
@endsection
