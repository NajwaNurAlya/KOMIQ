<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicChapterController extends Controller
{
    public function show(Request $request, Comic $comic, int $chapterNumber): View
    {
        $chapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', $chapterNumber)
            ->firstOrFail();

        $isCreator = auth()->check() && $comic->user_id === auth()->id();
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        if (! $chapter->isApproved() && ! $isCreator && ! $isAdmin) {
            abort(404, 'Chapter tidak ditemukan atau belum dipublikasikan.');
        }

        $allApprovedChapters = Chapter::where('comic_id', $comic->id)
            ->approved()
            ->orderBy('chapter_number', 'asc')
            ->get();

        $prevChapter = Chapter::where('comic_id', $comic->id)
            ->approved()
            ->where('chapter_number', '<', $chapter->chapter_number)
            ->orderBy('chapter_number', 'desc')
            ->first();

        $nextChapter = Chapter::where('comic_id', $comic->id)
            ->approved()
            ->where('chapter_number', '>', $chapter->chapter_number)
            ->orderBy('chapter_number', 'asc')
            ->first();

        $isUnlocked = ! $chapter->is_premium || $isCreator || $isAdmin
            || (auth()->check() && auth()->user()->chapterAccesses()->where('chapter_id', $chapter->id)->exists());

        $userCoinBalance = (int) auth()->user()?->coin_balance;

        $comments = $chapter->comments()->where('is_hidden', false)->with('user')->latest()->get();

        return view('chapters.reader', compact(
            'comic',
            'chapter',
            'allApprovedChapters',
            'prevChapter',
            'nextChapter',
            'isUnlocked',
            'isCreator',
            'isAdmin',
            'userCoinBalance',
            'comments'
        ));
    }

    public function unlock(Request $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id || ! $chapter->is_premium || ! $chapter->isApproved()) {
            abort(404);
        }

        $hasil = DB::transaction(function () use ($request, $comic, $chapter) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->first();

            if ($user->chapterAccesses()->where('chapter_id', $chapter->id)->exists()) {
                return 'sudah';
            }

            if ($user->coin_balance < $chapter->coin_price) {
                return 'kurang';
            }

            $user->decrement('coin_balance', $chapter->coin_price);

            $trx = $user->coinTransactions()->create([
                'type' => 'purchase',
                'amount' => -$chapter->coin_price,
                'balance_after' => $user->coin_balance,
                'description' => "{$comic->title} - Chapter {$chapter->chapter_number}",
            ]);

            $user->chapterAccesses()->create([
                'chapter_id' => $chapter->id,
                'coin_transaction_id' => $trx->id,
            ]);

            return 'berhasil';
        });

        return redirect()
            ->route('comics.chapters.show', [$comic->id, $chapter->chapter_number])
            ->with(...match ($hasil) {
                'berhasil' => ['success', "Chapter {$chapter->chapter_number} berhasil dibuka dengan {$chapter->coin_price} koin."],
                'sudah' => ['warning', "Chapter {$chapter->chapter_number} sudah pernah Anda buka."],
                'kurang' => ['error', 'Saldo koin tidak mencukupi. Silakan top up terlebih dahulu.'],
            });
    }
}
