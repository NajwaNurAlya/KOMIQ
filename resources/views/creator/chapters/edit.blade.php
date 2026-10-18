@extends('layouts.app')

@section('title', 'Edit Chapter ' . $chapter->chapter_number . ' - ' . ($comic->title ?? 'Komik'))

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" style="color: var(--text-muted); font-size: 0.875rem;">
            ← Kembali ke Daftar Chapter
        </a>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
            <h1 style="font-size: 1.75rem;">Edit Chapter {{ $chapter->chapter_number }}</h1>
            <x-chapter-status :chapter="$chapter" />
        </div>
    </div>

    @if($chapter->isRejected() && $chapter->review_note)
        <div class="alert alert-error" style="margin-bottom: 1.5rem; display: block;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-weight: 700; color: #f87171;">
                <span><x-icon name="triangle-alert" /> Catatan Evaluasi dari Admin:</span>
            </div>
            <div style="background: rgba(0, 0, 0, 0.25); padding: 0.85rem; border-radius: 8px; color: #fecaca; font-size: 0.95rem; line-height: 1.5;">
                "{{ $chapter->review_note }}"
            </div>
            <div style="margin-top: 0.6rem; font-size: 0.85rem; color: #f87171;">
                Silakan lakukan revisi pada form di bawah, lalu simpan perubahan dan ajukan kembali chapter ini untuk ditinjau.
            </div>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('creator.comics.chapters.update', [$comic->id, $chapter->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="chapter_number" class="form-label">Nomor Chapter *</label>
                    <input type="number" id="chapter_number" name="chapter_number" class="form-control" 
                           value="{{ old('chapter_number', $chapter->chapter_number) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label for="title" class="form-label">Judul Chapter *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="{{ old('title', $chapter->title) }}" required>
                </div>
            </div>

            <!-- Halaman Yang Ada Saat Ini -->
            @if(is_array($chapter->pages) && count($chapter->pages) > 0)
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Halaman Komik Saat Ini ({{ count($chapter->pages) }} Halaman):</label>
                    <p style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        Hilangkan centang jika ingin menghapus halaman tertentu.
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                        @foreach($chapter->pages as $index => $pagePath)
                            <div style="position: relative; width: 100px; height: 135px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); background: #000;">
                                <img src="{{ asset('storage/' . $pagePath) }}" alt="Hal {{ $index + 1 }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.7); border-radius: 4px; padding: 2px 4px;">
                                    <input type="checkbox" name="existing_pages[]" value="{{ $pagePath }}" checked title="Pertahankan halaman ini" style="accent-color: #10b981;">
                                </div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.75); color: #fff; font-size: 10px; text-align: center; padding: 2px;">
                                    Hal {{ $index + 1 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tambah Halaman Baru -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label for="pages" class="form-label">Unggah Halaman Baru (Opsional)</label>
                <input type="file" id="pages" name="pages[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control">
                <small style="color: var(--text-muted); display: block; margin-top: 0.35rem;">
                    Halaman baru yang diunggah akan otomatis ditambahkan ke bagian akhir bab ini.
                </small>
            </div>

            <!-- Pengaturan Premium & Koin Simulasi -->
            <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin: 1.5rem 0;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 700; color: #fff;">Jadikan Chapter Premium</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            Chapter premium membutuhkan koin simulasi dari pembaca untuk dibuka kuncinya.
                        </div>
                    </div>
                    <div>
                        <input type="checkbox" id="is_premium" name="is_premium" value="1" 
                               style="width: 20px; height: 20px; accent-color: var(--accent-primary); cursor: pointer;"
                               {{ old('is_premium', $chapter->is_premium) ? 'checked' : '' }} onchange="toggleCoinPrice()">
                    </div>
                </div>

                <div id="coin_price_wrapper" style="margin-top: 1rem; {{ old('is_premium', $chapter->is_premium) ? '' : 'display: none;' }}">
                    <label for="coin_price" class="form-label">Harga Koin (Simulasi) *</label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="number" id="coin_price" name="coin_price" class="form-control" 
                               style="max-width: 200px;" value="{{ old('coin_price', $chapter->coin_price) }}" min="1">
                        <span style="color: var(--text-secondary); font-size: 0.9rem;"><x-icon name="coins" /> Koin</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="save" /> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCoinPrice() {
        const checkbox = document.getElementById('is_premium');
        const wrapper = document.getElementById('coin_price_wrapper');
        if (checkbox.checked) {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }
</script>
@endsection
