@props(['comic', 'info'])
<a href="{{ route('comics.chapters.show', [$comic->id, $comic->chapters->first()->chapter_number]) }}" class="group block min-w-0 text-zinc-100 hover:text-white">
    <div class="aspect-[3/4] overflow-hidden rounded-md bg-[var(--bg-card)]">
        @if($comic->cover_image)
            <img src="{{ asset('storage/' . $comic->cover_image) }}" alt="{{ $comic->title }}" loading="lazy"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @endif
    </div>
    <p class="mt-2 text-[11px] leading-4 text-zinc-500">{{ $comic->genres->first()?->name }}</p>
    <p class="truncate text-sm font-semibold leading-5">{{ $comic->title }}</p>
    <p class="text-[11px] font-bold leading-4 text-[var(--accent-text)]">{{ $info }}</p>
</a>
