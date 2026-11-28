<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\View\View;

class HubController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $comics = $user ? $user->comics()->withCount('chapters')->orderBy('title')->get() : collect();

        return view('creator.hub', compact('comics'));
    }
}
