<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        if ($chapter->comic_id !== $comic->id || ! $chapter->isApproved()) {
            abort(404);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ], [
            'content.required' => 'Komentar tidak boleh kosong.',
            'content.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        $chapter->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->to(route('comics.chapters.show', [$comic->id, $chapter->chapter_number]) . '#komentar')
            ->with('success', 'Komentar berhasil dikirim.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $user = $request->user();

        if ($comment->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $comment->delete();

        return redirect()->to(url()->previous() . '#komentar')->with('success', 'Komentar berhasil dihapus.');
    }

    public function report(Request $request, Comment $comment): RedirectResponse
    {
        if ($comment->is_hidden || $comment->user_id === $request->user()->id) {
            return back()->with('error', 'Komentar ini tidak dapat dilaporkan.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'in:Bahasa kasar,Spam,Pelecehan,Konten tidak pantas,Lainnya'],
        ]);

        $report = $comment->reports()->firstOrCreate(
            ['reporter_id' => $request->user()->id],
            ['reason' => $validated['reason'] ?? 'Komentar bermasalah', 'status' => 'pending']
        );

        return back()->with($report->wasRecentlyCreated ? 'success' : 'warning', $report->wasRecentlyCreated
            ? 'Komentar berhasil dilaporkan untuk ditinjau Admin.'
            : 'Anda sudah melaporkan komentar ini.');
    }
}
