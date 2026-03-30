<?php

namespace App\Http\Controllers\Web\Gym;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GymDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $gym = $request->user()->gym;

        $todayCheckins  = $gym->checkins()
            ->with('user')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $recentCheckins = $gym->checkins()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        $todayCount = $todayCheckins->count();
        $monthCount = $gym->checkins()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('gym.dashboard', compact('gym', 'todayCheckins', 'recentCheckins', 'todayCount', 'monthCount'));
    }
}
