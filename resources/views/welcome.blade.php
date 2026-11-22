@extends('layouts.app')

@section('title', 'KOMIQ - Platform Membaca & Publikasi Komik Berbasis Web')

@section('styles')
<style>
    .pill { padding: .4rem 1rem; border-radius: 9999px; font-size: .75rem; font-weight: 600; white-space: nowrap;
        background: var(--bg-card); color: var(--text-secondary); border: 1px solid var(--border-color); }
    .pill:hover { color: var(--text-primary); }
    .pill.aktif { background: #fff; color: #18181b; border-color: #fff; }
    .carousel { scrollbar-width: none; }
    .carousel::-webkit-scrollbar { display: none; }
    .geser { position: absolute; top: 38%; transform: translateY(-50%); width: 2.5rem; height: 2.5rem; border-radius: 9999px;
        display: grid; place-items: center; font-size: 1.25rem; background: #fff; color: #18181b; box-shadow: 0 4px 14px rgba(0,0,0,.45); }
</style>
@endsection

@section('content')
@if($terbaru->isEmpty())
    <div class="card py-16 text-center text-zinc-400">Belum ada komik yang terbit.</div>
@else
<div class="space-y-12">
    <section>
        <h2 class="mb-4 text-lg">Update Terbaru</h2>
        <div class="relative" data-carousel>
            <div class="carousel flex snap-x snap-mandatory gap-2 overflow-x-auto">
                @foreach($terbaru as $comic)
                    <div class="w-[calc((100%-1rem)/3)] shrink-0 snap-start md:w-[calc((100%-2rem)/5)]">
                        <x-comic-card :comic="$comic" :info="'Ch. ' . $comic->chapters->last()->chapter_number" />
                    </div>
                @endforeach
            </div>
            <button type="button" class="geser -left-4" data-geser="-1" aria-label="Sebelumnya"><x-icon name="chevron-left" /></button>
            <button type="button" class="geser -right-4" data-geser="1" aria-label="Berikutnya"><x-icon name="chevron-right" /></button>
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-lg">Komik berdasarkan Genre</h2>
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            @foreach($genres as $genre)
                <button type="button" class="pill {{ $loop->first ? 'aktif' : '' }}" data-tab="genre:{{ $genre->id }}">{{ $genre->name }}</button>
            @endforeach
        </div>
        @foreach($genres as $genre)
            <div data-panel="genre:{{ $genre->id }}" @unless($loop->first) hidden @endunless>
                <div class="grid grid-cols-3 gap-x-2 gap-y-6 md:grid-cols-6">
                    @foreach($perGenre[$genre->id] as $comic)
                        <x-comic-card :comic="$comic" :info="$comic->chapters->count() . ' chapter'" />
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <section>
        <h2 class="mb-4 text-lg">Komik Baru di KOMIQ</h2>
        <div class="grid grid-cols-3 gap-x-2 gap-y-6 md:grid-cols-5">
            @foreach($baru as $comic)
                <x-comic-card :comic="$comic" :info="$comic->status === 'completed' ? 'Tamat' : 'Berjalan'" />
            @endforeach
        </div>
    </section>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-tab]').forEach(tombol => tombol.addEventListener('click', () => {
        const grup = tombol.dataset.tab.split(':')[0] + ':';
        document.querySelectorAll(`[data-tab^="${grup}"]`).forEach(t => t.classList.toggle('aktif', t === tombol));
        document.querySelectorAll(`[data-panel^="${grup}"]`).forEach(p => p.hidden = p.dataset.panel !== tombol.dataset.tab);
    }));

    document.querySelectorAll('[data-carousel]').forEach(wadah => {
        const baris = wadah.querySelector('.carousel');
        const [kiri, kanan] = wadah.querySelectorAll('[data-geser]');
        const cek = () => {
            kiri.style.visibility = baris.scrollLeft > 0 ? 'visible' : 'hidden';
            kanan.style.visibility = baris.scrollLeft + baris.clientWidth < baris.scrollWidth - 1 ? 'visible' : 'hidden';
        };
        [kiri, kanan].forEach(t => t.addEventListener('click', () =>
            baris.scrollBy({ left: t.dataset.geser * baris.clientWidth, behavior: 'smooth' })));
        baris.addEventListener('scroll', cek);
        cek();
    });
</script>
@endsection
