<?php

use App\Http\Controllers\Admin\ChapterReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Creator\ChapterController as CreatorChapterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicChapterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KOMIQ Platform
|--------------------------------------------------------------------------
| Proyek Tugas Pemrograman Web Lanjut Kelompok 5
| Modul Autentikasi, Role, & Profil: Irma Nurhayati
| Modul Chapter & Moderasi Admin: Najwa Nur Alya
*/

// Halaman Utama / Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Modul Autentikasi (Jobdesk: Irma Nurhayati)
|--------------------------------------------------------------------------
| Rute khusus tamu (guest) untuk pendaftaran akun baru dan login.
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Modul Profil & Sesi Pengguna (Jobdesk: Irma Nurhayati)
|--------------------------------------------------------------------------
| Hanya dapat diakses oleh pengguna yang telah login (auth).
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Modul Pembaca Chapter Publik (Jobdesk: Najwa Nur Alya)
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
| Modul Pengelolaan Chapter oleh Kreator (Jobdesk: Najwa Nur Alya)
|--------------------------------------------------------------------------
| Kreator mengelola chapter miliknya: membuat draft, mengunggah gambar,
| memperbaiki chapter yang ditolak, serta mengirimkan untuk ditinjau (Pending).
| Dilindungi autentikasi dan otorisasi role 'kreator' dan 'admin'.
*/
Route::prefix('creator/comics/{comic}')
    ->name('creator.comics.')
    ->middleware(['auth', 'role:kreator,admin'])
    ->group(function () {
        Route::resource('chapters', CreatorChapterController::class)->except(['show']);

        Route::post('chapters/{chapter}/submit', [CreatorChapterController::class, 'submit'])
            ->name('chapters.submit');
    });

/*
|--------------------------------------------------------------------------
| Modul Moderasi / Review Chapter oleh Admin (Jobdesk: Najwa Nur Alya)
|--------------------------------------------------------------------------
| Admin memeriksa chapter pending dari kreator, kemudian menyetujui (Approve)
| atau menolak (Reject) dengan menyertakan review_note.
| Dilindungi autentikasi dan otorisasi role 'admin'.
*/
Route::prefix('admin/chapter-reviews')
    ->name('admin.chapter-reviews.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [ChapterReviewController::class, 'index'])->name('index');
        Route::get('/{chapter}', [ChapterReviewController::class, 'show'])->name('show');
        Route::post('/{chapter}/approve', [ChapterReviewController::class, 'approve'])->name('approve');
        Route::post('/{chapter}/reject', [ChapterReviewController::class, 'reject'])->name('reject');
    });