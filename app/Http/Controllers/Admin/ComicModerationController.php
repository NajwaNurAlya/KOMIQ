<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;
class ComicModerationController extends Controller {
    public function index(Request $request): View { $visibility=$request->query('visibility','all'); if(!in_array($visibility,['all','visible','hidden'],true)) $visibility='all'; $query=Comic::with('user')->withCount('chapters')->latest(); if($visibility!=='all') $query->where('is_hidden',$visibility==='hidden'); return view('admin.comics.index',['comics'=>$query->paginate(15)->withQueryString(),'visibility'=>$visibility,'counts'=>['all'=>Comic::count(),'visible'=>Comic::where('is_hidden',false)->count(),'hidden'=>Comic::where('is_hidden',true)->count()]]); }
    public function show(Comic $comic): View { $comic->load(['user','genres','chapters'=>fn($q)=>$q->latest('chapter_number')]); return view('admin.comics.show',compact('comic')); }
    public function hide(Comic $comic): RedirectResponse { $comic->update(['is_hidden'=>true]); return back()->with('success', 'Komik disembunyikan dari halaman publik tanpa menghapus data.'); }
    public function unhide(Comic $comic): RedirectResponse { $comic->update(['is_hidden'=>false]); return back()->with('success', 'Komik kembali ditampilkan untuk publik.'); }
}
