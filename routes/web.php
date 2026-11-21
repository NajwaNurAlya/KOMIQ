<?php

use App\Http\Controllers\Admin\ChapterReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Creator\ChapterController as CreatorChapterController;
use App\Http\Controllers\Creator\HubController as CreatorHubController;
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
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});

Route::prefix('comics/{comic}')->group(function () {
    Route::get('chapters/{chapter}', [PublicChapterController::class, 'show'])
        ->name('comics.chapters.show');

    Route::post('chapters/{chapter}/unlock', [PublicChapterController::class, 'unlockSimulation'])
        ->name('comics.chapters.unlock');
});

Route::get('/creator', [CreatorHubController::class, 'index'])
    ->middleware(['auth', 'role:kreator'])
    ->name('creator.hub');

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
