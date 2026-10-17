<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicChapterController extends Controller
{
    /**
     * Tampilkan pembaca chapter komik untuk publik.
     */
    public function show(Request $request, Comic $comic, int $chapterNumber): View
    {
        // Cari chapter berdasarkan nomor urut dan comic_id
        $chapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', $chapterNumber)
            ->firstOrFail();

        // Pemeriksaan keamanan: Jangan tampilkan chapter yang belum approved kepada publik
        $isCreator = auth()->check() && $comic->user_id === auth()->id();
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        if (! $chapter->isApproved() && ! $isCreator && ! $isAdmin) {
            abort(404, 'Chapter tidak ditemukan atau belum dipublikasikan.');
        }

        // Daftar seluruh chapter approved untuk navigasi pembaca
        $allApprovedChapters = Chapter::where('comic_id', $comic->id)
            ->approved()
            ->orderBy('chapter_number', 'asc')
            ->get();

        // Cari chapter sebelumnya dan berikutnya (hanya yang sudah approved)
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

        // Periksa status unlock chapter premium
        $isUnlocked = ! $chapter->is_premium || $isCreator || $isAdmin;
        $unlockedSessionKey = "unlocked_chapter_{$chapter->id}";

        if (session()->has($unlockedSessionKey)) {
            $isUnlocked = true;
        }

        // Saldo koin saat ini (dari akun user jika login, atau dari sesi simulasi untuk tamu)
        $userCoinBalance = auth()->check()
            ? (int) auth()->user()->coin_balance
            : (int) session('simulated_coin_balance', 50);

        return view('chapters.reader', compact(
            'comic',
            'chapter',
            'allApprovedChapters',
            'prevChapter',
            'nextChapter',
            'isUnlocked',
            'isCreator',
            'isAdmin',
            'userCoinBalance'
        ));
    }

    /**
     * Simulasi membuka chapter premium menggunakan koin (tanpa payment gateway).
     * Mencegah transaksi ganda dan memastikan saldo tidak menjadi negatif.
     */
    public function unlockSimulation(Request $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $unlockedSessionKey = "unlocked_chapter_{$chapter->id}";

        // 1. Cegah transaksi ganda jika chapter sudah terbuka
        if (session()->has($unlockedSessionKey)) {
            return redirect()
                ->route('comics.chapters.show', [$comic->id, $chapter->chapter_number])
                ->with('warning', "Chapter {$chapter->chapter_number} sudah pernah dibuka sebelumnya (tidak ada pemotongan koin ganda).");
        }

        // 2. Dapatkan saldo koin saat ini
        if (auth()->check()) {
            $user = auth()->user();
            $currentBalance = (int) $user->coin_balance;
        } else {
            $currentBalance = (int) session('simulated_coin_balance', 50);
        }

        $price = (int) $chapter->coin_price;

        // 3. Validasi kecukupan saldo: Mencegah saldo negatif
        if ($currentBalance < $price) {
            return redirect()
                ->route('comics.chapters.show', [$comic->id, $chapter->chapter_number])
                ->with('error', "Saldo koin tidak mencukupi! Anda memiliki {$currentBalance} Koin, sedangkan chapter ini membutuhkan {$price} Koin.");
        }

        // 4. Potong koin secara aman dan pastikan saldo akhir >= 0
        $newBalance = max(0, $currentBalance - $price);

        if (auth()->check()) {
            auth()->user()->update(['coin_balance' => $newBalance]);
        }
        session(['simulated_coin_balance' => $newBalance]);

        // 5. Tandai chapter sebagai sudah dibuka
        session()->put($unlockedSessionKey, true);

        return redirect()
            ->route('comics.chapters.show', [$comic->id, $chapter->chapter_number])
            ->with('success', "Simulasi berhasil! Chapter {$chapter->chapter_number} berhasil dibuka dengan {$price} Koin. Sisa saldo koin Anda: {$newBalance} Koin.");
    }
}
