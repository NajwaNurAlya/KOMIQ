@extends('layouts.app')

@section('title', 'Unggah Chapter Baru - ' . ($comic->title ?? 'Komik'))

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" style="color: var(--text-muted); font-size: 0.875rem;">
            ← Kembali ke Daftar Chapter
        </a>
        <h1 style="font-size: 1.75rem; margin-top: 0.5rem;">Unggah Chapter Baru</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Chapter baru akan otomatis disimpan sebagai <strong>Draft</strong> sebelum Anda mengajukannya ke Admin.
        </p>
    </div>

    <div class="card">
        <form action="{{ route('creator.comics.chapters.store', $comic->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="comic_id" value="{{ $comic->id }}">

            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="chapter_number" class="form-label">Nomor Chapter *</label>
                    <input type="number" id="chapter_number" name="chapter_number" class="form-control" 
                           value="{{ old('chapter_number', $nextChapterNumber) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label for="title" class="form-label">Judul Chapter *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="Contoh: Awal Perjalanan, Pertemuan Tak Terduga..." 
                           value="{{ old('title') }}" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 0.5rem;">
                <label for="pages" class="form-label">Unggah Gambar Halaman Komik (Pages) *</label>
                <div style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 2rem; text-align: center; background: var(--bg-secondary); cursor: pointer;"
                     onclick="document.getElementById('pages').click();">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"><x-icon name="image" stroke-width="1.5" style="color: var(--text-muted)" /></div>
                    <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                        Klik untuk memilih berkas gambar komik
                    </p>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">
                        Pilih satu atau beberapa gambar sekaligus (JPEG, PNG, JPG, WEBP). Maksimal 5 MB per berkas.
                    </p>
                    <input type="file" id="pages" name="pages[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" 
                           style="display: none;" onchange="previewImages(event)">
                </div>
                <div id="file-count" style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--accent-text); font-weight: 600;"></div>
                <div id="preview-container" style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem;"></div>
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
                               {{ old('is_premium') ? 'checked' : '' }} onchange="toggleCoinPrice()">
                    </div>
                </div>

                <div id="coin_price_wrapper" style="margin-top: 1rem; {{ old('is_premium') ? '' : 'display: none;' }}">
                    <label for="coin_price" class="form-label">Harga Koin (Simulasi) *</label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="number" id="coin_price" name="coin_price" class="form-control" 
                               style="max-width: 200px;" value="{{ old('coin_price', 10) }}" min="1">
                        <span style="color: var(--text-secondary); font-size: 0.9rem;"><x-icon name="coins" /> Koin</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('creator.comics.chapters.index', $comic->id) }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="save" /> Simpan sebagai Draft
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

    function previewImages(event) {
        const files = event.target.files;
        const container = document.getElementById('preview-container');
        const countText = document.getElementById('file-count');
        container.innerHTML = '';

        if (files.length > 0) {
            countText.innerText = files.length + ' halaman gambar dipilih:';
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = document.createElement('div');
                    box.style.position = 'relative';
                    box.style.width = '90px';
                    box.style.height = '120px';
                    box.style.borderRadius = '8px';
                    box.style.overflow = 'hidden';
                    box.style.border = '1px solid var(--border-color)';
                    box.style.background = '#000';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';

                    const label = document.createElement('div');
                    label.style.position = 'absolute';
                    label.style.bottom = '0';
                    label.style.left = '0';
                    label.style.right = '0';
                    label.style.background = 'rgba(0,0,0,0.7)';
                    label.style.color = '#fff';
                    label.style.fontSize = '10px';
                    label.style.textAlign = 'center';
                    label.style.padding = '2px 0';
                    label.innerText = 'Hal ' + (i + 1);

                    box.appendChild(img);
                    box.appendChild(label);
                    container.appendChild(box);
                }
                reader.readAsDataURL(file);
            }
        } else {
            countText.innerText = '';
        }
    }
</script>
@endsection
