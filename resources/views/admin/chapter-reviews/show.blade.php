@extends('layouts.app')

@section('title', 'Periksa Chapter: ' . $chapter->title . ' - Moderasi Admin')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.chapter-reviews.index') }}" style="color: var(--text-muted); font-size: 0.875rem;">
        ← Kembali ke Antrean Moderasi
    </a>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 0.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.875rem; color: #818cf8; font-weight: 600;">
                {{ $chapter->comic?->title ?? 'Komik' }} • Oleh {{ $chapter->comic?->user?->name ?? 'Kreator' }}
            </div>
            <h1 style="font-size: 1.85rem; margin-top: 0.25rem;">
                Chapter {{ $chapter->chapter_number }}: {{ $chapter->title }}
            </h1>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            @if($chapter->isDraft())
                <span class="badge badge-draft">Draft</span>
            @elseif($chapter->isPending())
                <span class="badge badge-pending">Menunggu Review</span>
            @elseif($chapter->isApproved())
                <span class="badge badge-approved">Approved</span>
            @elseif($chapter->isRejected())
                <span class="badge badge-rejected">Rejected</span>
            @endif

            @if($chapter->is_premium)
                <span class="badge badge-premium">🪙 {{ $chapter->coin_price }} Koin</span>
            @endif
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start;">
    <!-- Bagian Kiri: Pratinjau Halaman Chapter Komik -->
    <div>
        <div class="card" style="padding: 1.25rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 600; color: #fff;">
                🖼️ Halaman Komik (Total: {{ $chapter->page_count }} Halaman)
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Urutan tampilan dari atas ke bawah
            </div>
        </div>

        @if(is_array($chapter->pages) && count($chapter->pages) > 0)
            <div style="background: #000; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; align-items: center; padding: 1rem 0;">
                @foreach($chapter->pages as $index => $pagePath)
                    <div style="width: 100%; max-width: 760px; text-align: center; margin-bottom: 0.75rem; position: relative;">
                        <img src="{{ asset('storage/' . $pagePath) }}" alt="Halaman {{ $index + 1 }}" 
                             style="width: 100%; height: auto; display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
                        <div style="background: rgba(0,0,0,0.6); color: #94a3b8; font-size: 0.75rem; padding: 4px; border-radius: 4px; margin-top: 4px; display: inline-block;">
                            Halaman {{ $index + 1 }} dari {{ count($chapter->pages) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⚠️</div>
                <p>Tidak ada berkas halaman gambar yang tersimpan untuk chapter ini.</p>
            </div>
        @endif
    </div>

    <!-- Bagian Kanan: Panel Aksi Moderasi Admin -->
    <div style="position: sticky; top: 5rem;">
        <div class="card">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                ⚖️ Keputusan Moderasi
            </h3>

            @if($chapter->isApproved())
                <div class="alert alert-success" style="margin-bottom: 1rem; font-size: 0.875rem;">
                    <div>
                        <strong>✅ Telah Disetujui</strong>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                            Disetujui pada: {{ $chapter->reviewed_at?->format('d M Y, H:i') }}<br>
                            Pemeriksa: {{ $chapter->reviewer?->name ?? 'Admin' }}
                        </div>
                    </div>
                </div>
            @elseif($chapter->isRejected())
                <div class="alert alert-error" style="margin-bottom: 1rem; font-size: 0.875rem;">
                    <div>
                        <strong>❌ Telah Ditolak</strong>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                            Catatan: "{{ $chapter->review_note }}"<br>
                            Ditolak pada: {{ $chapter->reviewed_at?->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Setujui (Approve) -->
            <form action="{{ route('admin.chapter-reviews.approve', $chapter->id) }}" method="POST" style="margin-bottom: 1rem;"
                  onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI chapter ini? Chapter akan langsung dipublikasikan untuk pembaca.');">
                @csrf
                <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                    ✅ Setujui Chapter (Approve)
                </button>
            </form>

            <!-- Tombol Buka Form Tolak (Reject) -->
            <button type="button" class="btn btn-danger" style="width: 100%; justify-content: center; padding: 0.75rem;"
                    onclick="toggleRejectForm()">
                ❌ Tolak Chapter (Reject)
            </button>

            <!-- Form Tolak dengan Review Note -->
            <div id="reject-form-wrapper" style="margin-top: 1.25rem; display: {{ $errors->has('review_note') ? 'block' : 'none' }}; border-top: 1px dashed var(--border-color); padding-top: 1rem;">
                <form action="{{ route('admin.chapter-reviews.reject', $chapter->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="review_note" class="form-label" style="color: #f87171;">
                            Catatan Evaluasi / Alasan Penolakan *
                        </label>
                        <textarea id="review_note" name="review_note" class="form-control" rows="4" 
                                  placeholder="Tuliskan catatan perbaikan untuk kreator (contoh: Kualitas gambar buram pada halaman 3, terdapat konten tidak layak, dll)..." required>{{ old('review_note', $chapter->review_note) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                        Kirim Penolakan & Catatan
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Detail Chapter -->
        <div class="card" style="font-size: 0.875rem;">
            <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--text-muted); text-transform: uppercase;">
                Informasi Pengajuan
            </h4>
            <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">ID Chapter:</span>
                    <span>#{{ $chapter->id }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Nomor Urut:</span>
                    <span>Chapter {{ $chapter->chapter_number }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Tipe Akses:</span>
                    <span>{{ $chapter->is_premium ? 'Premium (' . $chapter->coin_price . ' Koin)' : 'Gratis' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Diunggah:</span>
                    <span>{{ $chapter->created_at?->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleRejectForm() {
        const form = document.getElementById('reject-form-wrapper');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            document.getElementById('review_note').focus();
        } else {
            form.style.display = 'none';
        }
    }
</script>
@endsection
