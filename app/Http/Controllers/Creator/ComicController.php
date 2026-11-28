<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComicController extends Controller
{
  
    protected function authorizeComicAccess(Comic $comic): void
    {
        if (! auth()->check()) {
            abort(403, 'Akses ditolak: Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (! $user->isKreator() || ($comic->user_id !== null && (int) $comic->user_id !== (int) $user->id)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mengelola komik ini.');
        }
    }

   
    public function index(Request $request): View
    {
        $query = auth()->user()->comics()
            ->with(['genres'])
            ->withCount('chapters');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereLike('title', "%{$search}%")
                  ->orWhereLike('synopsis', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $comics = $query->latest()->paginate(12)->withQueryString();

        return view('creator.comics.index', compact('comics'));
    }

   
    public function create(): View
    {
        $genres = Genre::orderBy('name', 'asc')->get();

        return view('creator.comics.create', compact('genres'));
    }

    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'status'      => 'required|in:ongoing,completed',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'genre_ids'   => 'required|array|min:1',
            'genre_ids.*' => 'exists:genres,id',
        ], [
            'title.required'     => 'Judul komik wajib diisi.',
            'title.max'          => 'Judul komik maksimal 255 karakter.',
            'status.required'    => 'Status komik wajib dipilih.',
            'status.in'          => 'Status komik harus ongoing atau completed.',
            'genre_ids.required' => 'Pilih minimal satu genre untuk komik ini.',
            'genre_ids.min'      => 'Pilih minimal satu genre untuk komik ini.',
            'genre_ids.*.exists' => 'Genre yang dipilih tidak valid.',
            'cover_image.image'  => 'Sampul harus berupa berkas gambar.',
            'cover_image.mimes'  => 'Format sampul yang didukung: JPEG, PNG, JPG, WEBP.',
            'cover_image.max'    => 'Ukuran file sampul maksimal 2 MB.',
            'banner_image.image' => 'Banner harus berupa berkas gambar.',
            'banner_image.mimes' => 'Format banner yang didukung: JPEG, PNG, JPG, WEBP.',
            'banner_image.max'   => 'Ukuran file banner maksimal 2 MB.',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        // Generate unique slug dari judul
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug . '-' . Str::lower(Str::random(5));
        while (Comic::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . Str::lower(Str::random(5));
        }

        $comic = Comic::create([
            'user_id'     => auth()->id(),
            'title'       => $validated['title'],
            'slug'        => $slug,
            'synopsis'    => $validated['synopsis'] ?? null,
            'cover_image' => $coverPath,
            'banner_image' => $request->file('banner_image')?->store('banners', 'public'),
            'status'      => $validated['status'],
            'is_hidden'   => false,
        ]);

        if (! empty($validated['genre_ids'])) {
            $comic->genres()->sync($validated['genre_ids']);
        }

        return redirect()
            ->route('creator.comics.index')
            ->with('success', 'Komik "' . $comic->title . '" berhasil dibuat!');
    }

    
    public function show(Comic $comic): RedirectResponse
    {
        $this->authorizeComicAccess($comic);

        return redirect()->route('creator.comics.chapters.index', $comic->id);
    }

    public function edit(Comic $comic): View
    {
        $this->authorizeComicAccess($comic);

        $genres = Genre::orderBy('name', 'asc')->get();
        $comic->load('genres');

        return view('creator.comics.edit', compact('comic', 'genres'));
    }

    
    public function update(Request $request, Comic $comic): RedirectResponse
    {
        $this->authorizeComicAccess($comic);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'status'      => 'required|in:ongoing,completed',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'genre_ids'   => 'required|array|min:1',
            'genre_ids.*' => 'exists:genres,id',
        ], [
            'title.required'     => 'Judul komik wajib diisi.',
            'title.max'          => 'Judul komik maksimal 255 karakter.',
            'status.required'    => 'Status komik wajib dipilih.',
            'status.in'          => 'Status komik harus ongoing atau completed.',
            'genre_ids.required' => 'Pilih minimal satu genre untuk komik ini.',
            'genre_ids.min'      => 'Pilih minimal satu genre untuk komik ini.',
            'genre_ids.*.exists' => 'Genre yang dipilih tidak valid.',
            'cover_image.image'  => 'Sampul harus berupa berkas gambar.',
            'cover_image.mimes'  => 'Format sampul yang didukung: JPEG, PNG, JPG, WEBP.',
            'cover_image.max'    => 'Ukuran file sampul maksimal 2 MB.',
            'banner_image.image' => 'Banner harus berupa berkas gambar.',
            'banner_image.mimes' => 'Format banner yang didukung: JPEG, PNG, JPG, WEBP.',
            'banner_image.max'   => 'Ukuran file banner maksimal 2 MB.',
        ]);

        $data = [
            'title'    => $validated['title'],
            'synopsis' => $validated['synopsis'] ?? null,
            'status'   => $validated['status'],
        ];
      
        if ($comic->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug . '-' . Str::lower(Str::random(5));
            while (Comic::where('slug', $slug)->where('id', '!=', $comic->id)->exists()) {
                $slug = $baseSlug . '-' . Str::lower(Str::random(5));
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('cover_image')) {
            if ($comic->cover_image && Storage::disk('public')->exists($comic->cover_image)) {
                Storage::disk('public')->delete($comic->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('banner_image')) {
            if ($comic->banner_image) {
                Storage::disk('public')->delete($comic->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        }

        $comic->update($data);

        $comic->genres()->sync($validated['genre_ids']);

        return redirect()
            ->route('creator.comics.index')
            ->with('success', 'Komik "' . $comic->title . '" berhasil diperbarui!');
    }

   
    public function destroy(Comic $comic): RedirectResponse
    {
        $this->authorizeComicAccess($comic);

        $title = $comic->title;


        if ($comic->cover_image && Storage::disk('public')->exists($comic->cover_image)) {
            Storage::disk('public')->delete($comic->cover_image);
        }

        if ($comic->banner_image) {
            Storage::disk('public')->delete($comic->banner_image);
        }

        foreach ($comic->chapters as $chapter) {
            if (is_array($chapter->pages)) {
                foreach ($chapter->pages as $pagePath) {
                    if (Storage::disk('public')->exists($pagePath)) {
                        Storage::disk('public')->delete($pagePath);
                    }
                }
            }
        }

        $comic->delete();

        return redirect()
            ->route('creator.comics.index')
            ->with('success', 'Komik "' . $title . '" berhasil dihapus!');
    }
}
