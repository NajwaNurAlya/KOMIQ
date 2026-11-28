<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        if (! in_array($status, ['all', 'pending', 'visible', 'hidden'], true)) $status = 'pending';

        $query = Comment::with(['user', 'chapter.comic', 'reports' => fn ($reports) => $reports->where('status', 'pending')->with('reporter')])->latest();
        match ($status) {
            'pending' => $query->where('is_hidden', false)->whereHas('reports', fn ($reports) => $reports->where('status', 'pending')),
            'visible' => $query->where('is_hidden', false)->whereDoesntHave('reports', fn ($reports) => $reports->where('status', 'pending')),
            'hidden' => $query->where('is_hidden', true),
            default => null,
        };

        return view('admin.comments.index', [
            'comments' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'counts' => [
                'all' => Comment::count(),
                'pending' => Comment::where('is_hidden', false)->whereHas('reports', fn ($reports) => $reports->where('status', 'pending'))->count(),
                'visible' => Comment::where('is_hidden', false)->whereDoesntHave('reports', fn ($reports) => $reports->where('status', 'pending'))->count(),
                'hidden' => Comment::where('is_hidden', true)->count(),
            ],
        ]);
    }

    public function markReviewed(Comment $comment): RedirectResponse
    {
        if ($comment->is_hidden) return back()->with('warning', 'Komentar yang disembunyikan tidak perlu ditandai tampil.');
        $this->resolveReports($comment);
        return back()->with('success', 'Laporan komentar ditutup dan komentar tetap tampil untuk pembaca.');
    }

    public function hide(Comment $comment): RedirectResponse
    {
        $this->resolveReports($comment);
        $comment->update(['is_hidden' => true]);
        return back()->with('success', 'Komentar disembunyikan tanpa menghapus data.');
    }

    public function unhide(Comment $comment): RedirectResponse
    {
        $comment->update(['is_hidden' => false]);
        return back()->with('success', 'Komentar dipulihkan dan tetap tersimpan.');
    }

    private function resolveReports(Comment $comment): void
    {
        $now = now();
        $comment->reports()->where('status', 'pending')->update(['status' => 'resolved', 'reviewed_by' => auth()->id(), 'reviewed_at' => $now]);
        $comment->update(['is_reviewed' => true, 'reviewed_by' => auth()->id(), 'reviewed_at' => $now]);
    }
}
