<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComicController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comic::with('genres', 'user')
            ->withCount(['chapters' => fn ($q) => $q->approved()])
            ->where('is_hidden', false)
            ->whereHas('chapters', fn ($q) => $q->approved());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereLike('title', "%{$search}%")
                  ->orWhereLike('synopsis', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $genreSlug = $request->genre;
            $query->whereHas('genres', function ($q) use ($genreSlug) {
                $q->where('slug', $genreSlug);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kreator')) {
            $query->where('user_id', $request->kreator);
        }

        return view('comics.index', [
            'comics' => $query->orderBy('title')->paginate(18)->withQueryString(),
            'genres' => Genre::orderBy('name')->get(),
            'kreators' => User::whereHas('comics')->orderBy('name')->get(),
        ]);
    }

    public function show(Comic $comic): View
    {
        abort_if($comic->is_hidden, 404);

        $comic->load(['genres', 'chapters' => fn ($q) => $q->approved()->orderBy('chapter_number')]);

        $rekomendasi = Comic::where('id', '!=', $comic->id)
            ->where('is_hidden', false)
            ->whereHas('chapters', fn ($q) => $q->approved())
            ->with('genres')
            ->withCount([
                'chapters' => fn ($q) => $q->approved(),
                'genres as genre_sama' => fn ($q) => $q->whereIn('genres.id', $comic->genres->pluck('id')),
            ])
            ->orderByDesc('genre_sama')
            ->take(5)
            ->get();

        return view('comics.show', compact('comic', 'rekomendasi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:ongoing,completed',
            'genre_ids'   => 'required|array',
            'genre_ids.*' => 'exists:genres,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);

        $comic = Comic::create($validated);

        $comic->genres()->attach($request->genre_ids);

        return response()->json([
            'message' => 'Komik berhasil dibuat',
            'data'    => $comic->load('genres')
        ], 201);
    }

    public function update(Request $request, Comic $comic)
    {
        if ($comic->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'      => 'required|in:ongoing,completed',
            'genre_ids'   => 'required|array',
            'genre_ids.*' => 'exists:genres,id',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($comic->cover_image) {
                Storage::disk('public')->delete($comic->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($comic->title !== $request->title) {
            $validated['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        $comic->update($validated);

        $comic->genres()->sync($request->genre_ids);

        return response()->json([
            'message' => 'Komik berhasil diperbarui',
            'data'    => $comic->load('genres')
        ]);
    }

    public function destroy(Comic $comic)
    {
        if ($comic->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($comic->cover_image) {
            Storage::disk('public')->delete($comic->cover_image);
        }

        $comic->delete();

        return response()->json([
            'message' => 'Komik berhasil dihapus'
        ]);
    }
}
