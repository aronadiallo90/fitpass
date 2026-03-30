<?php

namespace App\Http\Controllers\Web\Gym;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GymCheckinController extends Controller
{
    public function __invoke(Request $request): View
    {
        $gym = $request->user()->gym;

        $checkins = $gym->checkins()
            ->with('user')
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(25);

        return view('gym.checkins', compact('checkins'));
    }
}
