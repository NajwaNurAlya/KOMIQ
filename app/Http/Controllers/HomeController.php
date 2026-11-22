<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Genre;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $komik = Comic::where('is_hidden', false)
            ->whereHas('chapters', fn ($q) => $q->approved())
            ->with(['genres', 'chapters' => fn ($q) => $q->approved()->orderBy('chapter_number')])
            ->get();

        $genres = Genre::withCount('comics')->orderByDesc('comics_count')->take(8)->get();

        return view('welcome', [
            'terbaru' => $komik->sortByDesc(fn ($c) => $c->chapters->max('published_at'))->take(10),
            'baru' => $komik->sortByDesc('id')->take(5),
            'genres' => $genres,
            'perGenre' => $genres->mapWithKeys(fn ($g) => [
                $g->id => $komik->filter(fn ($c) => $c->genres->contains($g))->take(6),
            ]),
        ]);
    }
}
