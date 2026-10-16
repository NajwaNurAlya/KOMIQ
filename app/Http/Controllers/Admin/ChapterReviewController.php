<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewChapterRequest;
use App\Models\Chapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChapterReviewController extends Controller
{
    /**
     * Memastikan hanya pengguna dengan peran admin yang dapat mengakses fitur moderasi.
     */
    protected function authorizeAdminAccess(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak: Fitur moderasi dan peninjauan chapter hanya dapat diakses oleh Administrator.');
        }
    }

    /**
     * Tampilkan antrean chapter yang memerlukan peninjauan oleh Admin.
     */
    public function index(Request $request): View
    {
        $this->authorizeAdminAccess();

        $statusFilter = $request->query('status', Chapter::STATUS_PENDING);

        $query = Chapter::with(['comic.user', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $chapters = $query->paginate(15)->withQueryString();

        $counts = [
            'pending' => Chapter::where('status', Chapter::STATUS_PENDING)->count(),
            'approved' => Chapter::where('status', Chapter::STATUS_APPROVED)->count(),
            'rejected' => Chapter::where('status', Chapter::STATUS_REJECTED)->count(),
            'all' => Chapter::count(),
        ];

        return view('admin.chapter-reviews.index', compact('chapters', 'counts', 'statusFilter'));
    }

    /**
     * Tampilkan detail chapter dan seluruh halamannya untuk diperiksa Admin.
     */
    public function show(Chapter $chapter): View
    {
        $this->authorizeAdminAccess();

        $chapter->load(['comic.user', 'reviewer']);

        return view('admin.chapter-reviews.show', compact('chapter'));
    }

    /**
     * Setujui chapter (Approve) sehingga status menjadi approved dan siap dipublikasikan.
     */
    public function approve(Request $request, Chapter $chapter): RedirectResponse
    {
        $this->authorizeAdminAccess();

        if ($chapter->status !== Chapter::STATUS_PENDING) {
            return back()->with('error', "Chapter saat ini berstatus \"{$chapter->status}\" dan tidak dapat disetujui kembali.");
        }

        $adminId = auth()->id();

        $chapter->update([
            'status' => Chapter::STATUS_APPROVED,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.chapter-reviews.index', ['status' => Chapter::STATUS_PENDING])
            ->with('success', "Chapter {$chapter->chapter_number} (\"{$chapter->title}\") pada komik \"{$chapter->comic?->title}\" telah DISETUJUI dan dipublikasikan.");
    }

    /**
     * Tolak chapter (Reject) dengan menyertakan review_note (catatan perbaikan untuk Kreator).
     */
    public function reject(ReviewChapterRequest $request, Chapter $chapter): RedirectResponse
    {
        $this->authorizeAdminAccess();

        if ($chapter->status !== Chapter::STATUS_PENDING) {
            return back()->with('error', "Chapter saat ini berstatus \"{$chapter->status}\" dan tidak dapat ditolak.");
        }

        $validated = $request->validated();
        $adminId = auth()->id();

        $chapter->update([
            'status' => Chapter::STATUS_REJECTED,
            'review_note' => $validated['review_note'],
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.chapter-reviews.index', ['status' => Chapter::STATUS_PENDING])
            ->with('warning', "Chapter {$chapter->chapter_number} telah DITOLAK. Catatan evaluasi telah dikirimkan ke Kreator.");
    }
}
