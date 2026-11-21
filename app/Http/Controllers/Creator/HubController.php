<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HubController extends Controller
{
    public function index(): View
    {
        $comics = auth()->user()->comics()->withCount('chapters')->orderBy('title')->get();

        return view('creator.hub', compact('comics'));
    }
}
