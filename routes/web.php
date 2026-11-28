<?php

use App\Http\Controllers\Admin\ChapterReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Creator\ChapterController as CreatorChapterController;
use App\Http\Controllers\Creator\ComicController as CreatorComicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicChapterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile/{tab?}', [ProfileController::class, 'show'])
        ->whereIn('tab', ['keamanan', 'koin'])
        ->name('profile.show');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    Route::get('/koin', [CoinController::class, 'index'])
        ->name('coins.index');

    Route::post('/koin', [CoinController::class, 'store'])
        ->name('coins.store');

    Route::get('/koin/selesai', [CoinController::class, 'finish'])
        ->name('coins.finish');
});

Route::post('/midtrans/notification', [CoinController::class, 'notification'])
    ->name('midtrans.notification');

Route::get('/comics', [ComicController::class, 'index'])
    ->name('comics.index');

Route::prefix('comics/{comic}')->group(function () {
    Route::get('/', [ComicController::class, 'show'])
        ->name('comics.show');

    Route::get('chapters/{chapter}', [PublicChapterController::class, 'show'])
        ->name('comics.chapters.show');

    Route::post('chapters/{chapter}/unlock', [PublicChapterController::class, 'unlock'])
        ->middleware('auth')
        ->name('comics.chapters.unlock');

    Route::post('chapters/{chapter}/comments', [CommentController::class, 'store'])
        ->middleware('auth')
        ->name('comics.chapters.comments.store');
});

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
    ->middleware('auth')
    ->name('comments.destroy');

    Route::prefix('creator')
        ->name('creator.')
        ->middleware(['auth', 'role:kreator,admin'])
        ->group(function () {
            Route::resource('comics', CreatorComicController::class)
                ->except(['show']);
            });

    Route::prefix('creator/comics/{comic}')
    ->name('creator.comics.')
    ->middleware(['auth', 'role:kreator,admin'])
    ->group(function () {
        Route::resource('chapters', CreatorChapterController::class)
            ->except(['show']);

        Route::post('chapters/{chapter}/submit', [CreatorChapterController::class, 'submit'])
            ->name('chapters.submit');
    });

    Route::prefix('admin/chapter-reviews')
    ->name('admin.chapter-reviews.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [ChapterReviewController::class, 'index'])
            ->name('index');

        Route::get('/{chapter}', [ChapterReviewController::class, 'show'])
            ->name('show');

        Route::post('/{chapter}/approve', [ChapterReviewController::class, 'approve'])
            ->name('approve');

        Route::post('/{chapter}/reject', [ChapterReviewController::class, 'reject'])
            ->name('reject');
    });
