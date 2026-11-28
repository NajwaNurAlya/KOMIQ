@props(['wajib' => false, 'awalan' => 'Hal'])

<div data-awalan="{{ $awalan }}" data-maks="{{ ini_get('max_file_uploads') }}">
    <label class="relative flex cursor-pointer flex-col items-center gap-1 rounded-xl border-2 border-dashed border-[var(--border-color)] bg-[var(--bg-secondary)] px-6 py-8 text-center hover:border-zinc-500">
        <x-icon name="image" size="2.25rem" class="text-zinc-500" />
        <span class="font-semibold text-zinc-100">Klik atau seret gambar ke sini</span>
        <span class="text-xs text-zinc-500">JPG, PNG, WEBP · maks 5 MB per gambar · urutan halaman mengikuti nama file (01.jpg, 02.jpg, …)</span>
        <input type="file" name="pages[]" multiple accept="image/jpeg,image/png,image/webp" @required($wajib)
            class="absolute inset-0 cursor-pointer opacity-0" onchange="pratinjauHalaman(this)">
    </label>
    <p class="mt-2 text-sm font-semibold" data-info></p>
    <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-6" data-pratinjau></div>
</div>

@once
<script>
    function pratinjauHalaman(input) {
        const wadah = input.closest('[data-maks]');
        const files = [...input.files].sort((a, b) => a.name.localeCompare(b.name, undefined, { numeric: true }));
        const maks = Number(wadah.dataset.maks);
        const info = wadah.querySelector('[data-info]');

        info.className = 'mt-2 text-sm font-semibold ' + (files.length > maks ? 'text-amber-400' : 'text-red-400');
        info.textContent = !files.length ? ''
            : files.length > maks ? `${files.length} gambar dipilih, tapi maksimal ${maks} sekali unggah. Sisanya tambahkan lewat Edit Chapter.`
            : `${files.length} halaman dipilih`;

        wadah.querySelector('[data-pratinjau]').innerHTML = files.map((f, i) => `
            <figure class="relative aspect-[3/4] overflow-hidden rounded-md border border-[var(--border-color)] bg-black">
                <img src="${URL.createObjectURL(f)}" alt="" class="h-full w-full object-cover">
                <figcaption class="absolute inset-x-0 bottom-0 bg-black/75 py-0.5 text-center text-[10px] text-white">${wadah.dataset.awalan} ${i + 1}</figcaption>
            </figure>`).join('');
    }
</script>
@endonce
