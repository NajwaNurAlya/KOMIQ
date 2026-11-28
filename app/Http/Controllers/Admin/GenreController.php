<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        return view('admin.genres.index', [
            'genres' => Genre::withCount('comics')->orderBy('name')->paginate(20),
            'allGenres' => Genre::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:50', 'unique:genres,name']]);
        Genre::create(['name' => $validated['name'], 'slug' => $this->uniqueSlug($validated['name'])]);

        return back()->with('success', 'Genre berhasil ditambahkan.');
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:50', 'unique:genres,name,'.$genre->id]]);
        $genre->update(['name' => $validated['name'], 'slug' => $this->uniqueSlug($validated['name'], $genre->id)]);

        return back()->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $comicCount = $genre->comics()->count();
        $replacementId = request()->input('replacement_genre_id');

        if ($comicCount > 0 && ! $replacementId) {
            return back()->with('error', 'Pilih genre pengganti untuk memindahkan komik sebelum menghapus genre ini.');
        }

        if ($replacementId) {
            request()->validate([
                'replacement_genre_id' => ['required', 'integer', 'different:genre_id', 'exists:genres,id'],
            ]);

            if ((int) $replacementId === $genre->id) {
                return back()->with('error', 'Genre pengganti harus berbeda dari genre yang dihapus.');
            }
        }

        DB::transaction(function () use ($genre, $comicCount, $replacementId) {
            if ($comicCount > 0) {
                $replacement = Genre::findOrFail($replacementId);
                $genre->comics()->each(function ($comic) use ($genre, $replacement) {
                    $comic->genres()->syncWithoutDetaching([$replacement->id]);
                    $comic->genres()->detach($genre->id);
                });
            }
            $genre->delete();
        });

        return back()->with('success', $comicCount > 0
            ? "Genre berhasil dihapus dan {$comicCount} komik dipindahkan ke genre pengganti."
            : 'Genre berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($name), 45, '');
        $slug = $base ?: 'genre';
        $suffix = 2;

        while (Genre::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = Str::limit($base, 45 - strlen((string) $suffix), '') . '-' . $suffix++;
        }

        return $slug;
    }
}
