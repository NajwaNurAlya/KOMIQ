<?php

use App\Http\Controllers\Admin\ChapterReviewController;
use App\Http\Controllers\Creator\ChapterController as CreatorChapterController;
use App\Http\Controllers\PublicChapterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rute Pembaca Chapter Publik (KOMIQ)
|--------------------------------------------------------------------------
| Hanya chapter dengan status 'approved' yang dapat diakses oleh publik.
| Pembaca dapat membaca dan membuka chapter premium dengan simulasi koin.
*/
Route::prefix('comics/{comic}')->group(function () {
    Route::get('chapters/{chapter}', [PublicChapterController::class, 'show'])
        ->name('comics.chapters.show');

    Route::post('chapters/{chapter}/unlock', [PublicChapterController::class, 'unlockSimulation'])
        ->name('comics.chapters.unlock');
});

/*
|--------------------------------------------------------------------------
| Rute Pengelolaan Chapter oleh Kreator
|--------------------------------------------------------------------------
| Kreator mengelola chapter miliknya: membuat draft, mengunggah gambar,
| memperbaiki chapter yang ditolak, serta mengirimkan untuk ditinjau (Pending).
*/
Route::prefix('creator/comics/{comic}')->name('creator.comics.')->group(function () {
    Route::resource('chapters', CreatorChapterController::class)->except(['show']);

    Route::post('chapters/{chapter}/submit', [CreatorChapterController::class, 'submit'])
        ->name('chapters.submit');
});

/*
|--------------------------------------------------------------------------
| Rute Moderasi / Review Chapter oleh Admin
|--------------------------------------------------------------------------
| Admin memeriksa chapter pending dari kreator, kemudian menyetujui (Approve)
| atau menolak (Reject) dengan menyertakan review_note.
*/
Route::prefix('admin/chapter-reviews')->name('admin.chapter-reviews.')->group(function () {
    Route::get('/', [ChapterReviewController::class, 'index'])->name('index');
    Route::get('/{chapter}', [ChapterReviewController::class, 'show'])->name('show');
    Route::post('/{chapter}/approve', [ChapterReviewController::class, 'approve'])->name('approve');
    Route::post('/{chapter}/reject', [ChapterReviewController::class, 'reject'])->name('reject');
});
