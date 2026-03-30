<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymCheckin;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalMembers = User::where('role', 'member')
            ->where('is_active', true)
            ->count();

        $monthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_fcfa');

        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>=', now())
            ->count();

        $todayCheckins = GymCheckin::whereDate('created_at', today())
            ->where('status', 'valid')
            ->count();

        $recentPayments = Payment::with('subscription.plan', 'subscription.user')
            ->latest()
            ->limit(5)
            ->get();

        $recentMembers = User::where('role', 'member')
            ->with(['activeSubscription.plan'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers',
            'monthRevenue',
            'activeSubscriptions',
            'todayCheckins',
            'recentPayments',
            'recentMembers',
        ));
    }
}
