@extends('layouts.app')

@section('title', 'Edit Komik - ' . $comic->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('creator.comics.index') }}" style="color: var(--text-muted); font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            ← Kembali ke Komik Saya
        </a>
        <h1 style="font-size: 1.75rem; margin-top: 0.5rem;">Edit Identitas Komik</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Perbarui informasi metadata, genre, status, atau ganti gambar sampul untuk komik <strong>"{{ $comic->title }}"</strong>.
        </p>
    </div>

    <div class="card">
        <form action="{{ route('creator.comics.update', $comic->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="title" class="form-label">Judul Komik *</label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $comic->title) }}" required autofocus>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

          
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="synopsis" class="form-label">Sinopsis / Ringkasan Cerita</label>
                    <textarea id="synopsis" name="synopsis" rows="5" class="form-control @error('synopsis') is-invalid @enderror"
                              placeholder="Tuliskan sinopsis atau deskripsi komik...">{{ old('synopsis', $comic->synopsis) }}</textarea>
                    @error('synopsis')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status Serial Komik --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status Serial Komik *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="status" value="ongoing"
                                   style="accent-color: var(--accent-primary); width: 1.1rem; height: 1.1rem;"
                                   {{ old('status', $comic->status) === 'ongoing' ? 'checked' : '' }}>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #fff;">Berjalan (Ongoing)</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Komik masih aktif berlanjut rilis chapter baru.</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="status" value="completed"
                                   style="accent-color: var(--accent-primary); width: 1.1rem; height: 1.1rem;"
                                   {{ old('status', $comic->status) === 'completed' ? 'checked' : '' }}>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #fff;">Tamat (Completed)</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Cerita komik telah tamat secara keseluruhan.</div>
                            </div>
                        </label>
                    </div>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ganti Sampul / Cover Komik --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Gambar Sampul (Cover Image)</label>
                    
                    <div style="display: flex; gap: 1.5rem; align-items: flex-start; flex-wrap: wrap;">
                        {{-- Wadah Sampul Saat Ini & Pratinjau Baru --}}
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            <div id="cover-preview-wrapper" style="width: 140px; aspect-ratio: 3/4; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-primary); overflow: hidden; position: relative;">
                                @if($comic->cover_image)
                                    <img id="current-cover-image" src="{{ asset('storage/' . $comic->cover_image) }}" alt="{{ $comic->title }}"
                                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                @else
                                    <div id="no-cover-placeholder" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <x-icon name="image" size="2rem" />
                                        <div style="font-size: 0.75rem; margin-top: 0.25rem;">Tanpa Sampul</div>
                                    </div>
                                @endif
                                <img id="new-cover-image" src="" alt="Pratinjau Sampul Baru"
                                     style="display: none; width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span id="cover-label-indicator" style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $comic->cover_image ? 'Sampul Saat Ini' : 'Belum Ada Sampul' }}
                            </span>
                        </div>

                        {{-- Area Upload Pengganti Sampul --}}
                        <div style="flex: 1; min-width: 250px;">
                            <div style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 1.75rem 1rem; text-align: center; background: var(--bg-secondary); cursor: pointer; transition: border-color 0.2s;"
                                 onclick="document.getElementById('cover_image').click();"
                                 onmouseover="this.style.borderColor='var(--accent-text)'"
                                 onmouseout="this.style.borderColor='var(--border-color)'">
                                
                                <div style="font-size: 2.25rem; margin-bottom: 0.5rem;">
                                    <x-icon name="camera" size="2.25rem" style="color: var(--text-muted);" />
                                </div>
                                <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.9rem;">
                                    Klik untuk memilih gambar sampul baru
                                </p>
                                <p style="font-size: 0.8rem; color: var(--text-muted);">
                                    Format: JPEG, JPG, PNG, WEBP. Maksimum 2 MB.<br>
                                    <span style="color: var(--accent-text);">Kosongkan jika tidak ingin mengganti sampul.</span>
                                </p>
                                <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       style="display: none;" onchange="previewNewCover(event)">
                            </div>
                            
                            <div id="new-cover-note" style="display: none; margin-top: 0.5rem; font-size: 0.8rem; color: #34d399;">
                                <x-icon name="circle-check" /> Gambar sampul baru telah dipilih dan siap disimpan.
                            </div>
                        </div>
                    </div>

                    @error('cover_image')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Pilihan Checkbox Genre --}}
                @php
                    $selectedGenres = old('genre_ids', $comic->genres->pluck('id')->toArray());
                @endphp
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">
                        Pilih Genre Komik * <span style="font-weight: 400; color: var(--text-muted);">(Pilih satu atau beberapa genre)</span>
                    </label>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.6rem; padding: 1rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 10px; max-height: 240px; overflow-y: auto;">
                        @foreach($genres as $genre)
                            <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.45rem 0.65rem; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; transition: background 0.15s, border-color 0.15s;"
                                   onmouseover="this.style.borderColor='#71717a'"
                                   onmouseout="this.style.borderColor='var(--border-color)'">
                                <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                                       style="accent-color: var(--accent-primary); width: 16px; height: 16px; cursor: pointer;"
                                       {{ in_array($genre->id, $selectedGenres) ? 'checked' : '' }}>
                                <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $genre->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('genre_ids')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Tombol Aksi Bawah --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-secondary btn-sm" style="color: var(--text-muted);">
                    <x-icon name="list" /> Kelola Chapter ({{ $comic->chapters()->count() }})
                </a>

                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('creator.comics.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="save" /> Perbarui Komik
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewNewCover(event) {
        const file = event.target.files[0];
        const newImg = document.getElementById('new-cover-image');
        const currentImg = document.getElementById('current-cover-image');
        const placeholder = document.getElementById('no-cover-placeholder');
        const indicator = document.getElementById('cover-label-indicator');
        const note = document.getElementById('new-cover-note');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newImg.src = e.target.result;
                newImg.style.display = 'block';
                if (currentImg) currentImg.style.display = 'none';
                if (placeholder) placeholder.style.display = 'none';
                if (indicator) indicator.textContent = 'Pratinjau Sampul Baru';
                if (note) note.style.display = 'inline-flex';
            };
            reader.readAsDataURL(file);
        } else {
            newImg.style.display = 'none';
            if (currentImg) currentImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'flex';
            if (indicator) indicator.textContent = currentImg ? 'Sampul Saat Ini' : 'Belum Ada Sampul';
            if (note) note.style.display = 'none';
        }
    }
</script>
@endsection
