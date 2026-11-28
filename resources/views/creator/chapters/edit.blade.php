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

            @if(is_array($chapter->pages) && count($chapter->pages) > 0)
                <div class="form-group" style="margin-top: 1rem;">
                    <span class="form-label">Halaman Saat Ini ({{ count($chapter->pages) }})</span>
                    <p class="mb-3 text-xs text-zinc-500">Klik gambar untuk menghapus, klik lagi untuk membatalkan.</p>
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        @foreach($chapter->pages as $index => $pagePath)
                            <label class="relative aspect-[3/4] cursor-pointer overflow-hidden rounded-md border border-red-500 bg-black has-[:checked]:border-[var(--border-color)]">
                                <input type="checkbox" name="existing_pages[]" value="{{ $pagePath }}" checked class="peer sr-only">
                                <img src="{{ asset('storage/' . $pagePath) }}" alt="Halaman {{ $index + 1 }}" class="h-full w-full object-cover opacity-30 peer-checked:opacity-100">
                                <span class="absolute inset-x-0 bottom-0 hidden bg-black/75 py-0.5 text-center text-[10px] text-white peer-checked:block">Hal {{ $index + 1 }}</span>
                                <span class="absolute inset-x-0 bottom-0 bg-red-600 py-0.5 text-center text-[10px] font-semibold text-white peer-checked:hidden">Dihapus</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="form-group" style="margin-top: 1.5rem;">
                <span class="form-label">Tambah Halaman Baru (opsional)</span>
                <x-upload-halaman awalan="Baru" />
                <p class="mt-2 text-xs text-zinc-500">Halaman baru ditambahkan di bagian akhir chapter.</p>
            </div>

            <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin: 1.5rem 0;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 700; color: #fff;">Jadikan Chapter Premium</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            Pembaca harus membayar koin untuk membuka chapter ini.
                        </div>
                    </div>
                    <div>
                        <input type="checkbox" id="is_premium" name="is_premium" value="1" 
                               style="width: 20px; height: 20px; accent-color: var(--accent-primary); cursor: pointer;"
                               {{ old('is_premium', $chapter->is_premium) ? 'checked' : '' }} onchange="toggleCoinPrice()">
                    </div>
                </div>

                <div id="coin_price_wrapper" style="margin-top: 1rem; {{ old('is_premium', $chapter->is_premium) ? '' : 'display: none;' }}">
                    <label for="coin_price" class="form-label">Harga Koin *</label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="number" id="coin_price" name="coin_price" class="form-control" 
                               style="max-width: 200px;" value="{{ old('coin_price', $chapter->coin_price ?: 10) }}" min="1">
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
