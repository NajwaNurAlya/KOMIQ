<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
use App\Models\Chapter;
use App\Models\Comic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChapterController extends Controller
{
    protected function authorizeComicAccess(Comic $comic): void
    {
        if (! auth()->check()) {
            abort(403, 'Akses ditolak: Anda harus masuk (login) sebagai kreator pemilik komik ini.');
        }

        $user = auth()->user();

        if (! $user->isKreator() || ($comic->user_id !== null && (int) $comic->user_id !== (int) $user->id)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mengelola chapter pada komik milik kreator lain.');
        }
    }

    public function index(Request $request, Comic $comic): View
    {
        $this->authorizeComicAccess($comic);

        $chapters = $comic->chapters()
            ->orderBy('chapter_number', 'desc')
            ->paginate(15);

        $counts = [
            'all' => $comic->chapters()->count(),
            'draft' => $comic->chapters()->where('status', Chapter::STATUS_DRAFT)->count(),
            'pending' => $comic->chapters()->where('status', Chapter::STATUS_PENDING)->count(),
            'approved' => $comic->chapters()->where('status', Chapter::STATUS_APPROVED)->count(),
            'rejected' => $comic->chapters()->where('status', Chapter::STATUS_REJECTED)->count(),
        ];

        return view('creator.chapters.index', compact('comic', 'chapters', 'counts'));
    }

    public function create(Comic $comic): View
    {
        $this->authorizeComicAccess($comic);

        $nextChapterNumber = ($comic->chapters()->max('chapter_number') ?? 0) + 1;

        return view('creator.chapters.create', compact('comic', 'nextChapterNumber'));
    }

    public function store(StoreChapterRequest $request, Comic $comic): RedirectResponse
    {
        $this->authorizeComicAccess($comic);

        $validated = $request->validated();
        $chapterNumber = (int) $validated['chapter_number'];

        $pages = [];
        if ($request->hasFile('pages')) {
            $uploadedFiles = $request->file('pages');
            $pageIndex = 1;

            foreach ($uploadedFiles as $file) {
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $fileName = sprintf('page_%03d_%s.%s', $pageIndex, uniqid(), $extension);
                $directory = "comics/{$comic->id}/chapters/{$chapterNumber}";

                $path = $file->storeAs($directory, $fileName, 'public');
                $pages[] = $path;
                $pageIndex++;
            }
        }

        $chapter = Chapter::create([
            'comic_id' => $comic->id,
            'chapter_number' => $chapterNumber,
            'title' => $validated['title'],
            'pages' => $pages,
            'is_premium' => (bool) ($validated['is_premium'] ?? false),
            'coin_price' => (int) ($validated['coin_price'] ?? 0),
            'status' => Chapter::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('creator.comics.chapters.index', $comic->id)
            ->with('success', "Chapter {$chapter->chapter_number} (\"{$chapter->title}\") berhasil dibuat sebagai DRAFT. Silakan tinjau dan kirim untuk pemeriksaan Admin.");
    }

    public function edit(Comic $comic, Chapter $chapter): View|RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404, 'Chapter tidak ditemukan pada komik ini.');
        }

        $this->authorizeComicAccess($comic);

        if (! $chapter->canBeEdited()) {
            return redirect()
                ->route('creator.comics.chapters.index', $comic->id)
                ->with('error', "Chapter dengan status \"{$chapter->status}\" tidak dapat diubah.");
        }

        return view('creator.chapters.edit', compact('comic', 'chapter'));
    }

    public function update(UpdateChapterRequest $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $this->authorizeComicAccess($comic);

        if (! $chapter->canBeEdited()) {
            return back()->with('error', 'Hanya chapter berstatus Draft atau Ditolak (Rejected) yang dapat diperbaiki.');
        }

        $validated = $request->validated();
        $chapterNumber = (int) $validated['chapter_number'];

        $oldPages = is_array($chapter->pages) ? $chapter->pages : [];
        $pages = array_values(array_intersect($oldPages, (array) $request->input('existing_pages')));

        if ($request->hasFile('pages')) {
            $startIndex = count($pages) + 1;
            foreach ($request->file('pages') as $file) {
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $fileName = sprintf('page_%03d_%s.%s', $startIndex, uniqid(), $extension);
                $directory = "comics/{$comic->id}/chapters/{$chapterNumber}";

                $path = $file->storeAs($directory, $fileName, 'public');
                $pages[] = $path;
                $startIndex++;
            }
        }

        if (empty($pages)) {
            return back()->with('error', 'Chapter harus memiliki minimal 1 halaman gambar.');
        }

        $chapter->update([
            'chapter_number' => $chapterNumber,
            'title' => $validated['title'],
            'pages' => $pages,
            'is_premium' => (bool) ($validated['is_premium'] ?? false),
            'coin_price' => (int) ($validated['coin_price'] ?? 0),
        ]);

        foreach (array_diff($oldPages, $pages) as $removedFile) {
            Storage::disk('public')->delete($removedFile);
        }

        return redirect()
            ->route('creator.comics.chapters.index', $comic->id)
            ->with('success', "Chapter {$chapter->chapter_number} berhasil diperbarui.");
    }

    public function submit(Request $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $this->authorizeComicAccess($comic);

        if (! $chapter->canBeSubmitted()) {
            return back()->with('error', "Chapter saat ini berstatus \"{$chapter->status}\" dan tidak dapat diajukan kembali.");
        }

        if (empty($chapter->pages) || count($chapter->pages) === 0) {
            return back()->with('error', 'Chapter harus memiliki minimal 1 halaman sebelum diajukan ke Admin.');
        }

        $chapter->update([
            'status' => Chapter::STATUS_PENDING,
        ]);

        return redirect()
            ->route('creator.comics.chapters.index', $comic->id)
            ->with('success', "Chapter {$chapter->chapter_number} berhasil diajukan untuk ditinjau oleh Admin (Status: PENDING).");
    }

    public function destroy(Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $this->authorizeComicAccess($comic);

        if (! $chapter->canBeEdited()) {
            return back()->with('error', 'Hanya chapter berstatus Draft atau Ditolak yang dapat dihapus.');
        }

        if (is_array($chapter->pages)) {
            foreach ($chapter->pages as $pagePath) {
                Storage::disk('public')->delete($pagePath);
            }
        }

        $chapterNumber = $chapter->chapter_number;
        $chapter->delete();

        return redirect()
            ->route('creator.comics.chapters.index', $comic->id)
            ->with('success', "Chapter {$chapterNumber} berhasil dihapus.");
    }
}
