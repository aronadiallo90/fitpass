<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\GymActivity;
use Illuminate\View\View;

class GymDirectoryController extends Controller
{
    public function index(): View
    {
        $activities = GymActivity::orderBy('name')->get(['id', 'name', 'slug', 'icon']);
        $zones      = ['Plateau', 'Almadies', 'Mermoz', 'Parcelles', 'Guédiawaye', 'Thiès', 'Autre'];

        return view('member.gyms.index', compact('activities', 'zones'));
    }

    public function show(string $slug): View
    {
        return view('member.gyms.show', compact('slug'));
    }
}
