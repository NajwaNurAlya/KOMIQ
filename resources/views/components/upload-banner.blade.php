@props(['gambar' => null])

<label class="relative block aspect-[16/5] cursor-pointer overflow-hidden rounded-xl border-2 border-dashed border-[var(--border-color)] bg-[var(--bg-secondary)] hover:border-zinc-500">
    <img src="{{ $gambar }}" alt="" class="absolute inset-0 h-full w-full object-cover" @unless($gambar) hidden @endunless>
    <span class="absolute inset-0 flex flex-col items-center justify-center gap-1 bg-black/40 px-4 text-center text-sm font-semibold text-zinc-100">
        <x-icon name="image" size="1.75rem" />
        {{ $gambar ? 'Klik atau seret gambar untuk mengganti banner' : 'Klik atau seret gambar banner ke sini' }}
        <span class="text-xs font-normal text-zinc-300">Gambar lebar, misalnya 1600×500 · JPG, PNG, WEBP · maks 2 MB</span>
    </span>
    <input type="file" name="banner_image" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 cursor-pointer opacity-0"
        onchange="const img = this.parentElement.querySelector('img'); if (this.files[0]) { img.src = window.URL.createObjectURL(this.files[0]); img.hidden = false; }">
</label>
