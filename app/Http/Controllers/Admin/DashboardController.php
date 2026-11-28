<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Chapter, Comment, Comic, User};
use Illuminate\View\View;
class DashboardController extends Controller {
    public function __invoke(): View {
        $stats = ['users'=>User::count(), 'creators'=>User::where('role','kreator')->count(), 'comics'=>Comic::count(), 'chapters'=>Chapter::count(), 'pending'=>Chapter::pending()->count(), 'approved'=>Chapter::approved()->count(), 'rejected'=>Chapter::rejected()->count(), 'pendingComments'=>Comment::where('is_hidden',false)->whereHas('reports', fn ($reports) => $reports->where('status', 'pending'))->count(), 'hiddenComments'=>Comment::where('is_hidden',true)->count()];
        $pendingChapters = Chapter::with('comic.user')->pending()->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats','pendingChapters'));
    }
}
