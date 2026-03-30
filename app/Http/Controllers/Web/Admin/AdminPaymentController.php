<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    public function __invoke(Request $request): View
    {
        $payments = Payment::with('subscription.plan', 'subscription.user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->method, fn($q, $m) => $q->where('method', $m))
            ->latest()
            ->paginate(25);

        $totalRevenue = Payment::where('status', 'completed')->sum('amount_fcfa');

        return view('admin.payments', compact('payments', 'totalRevenue'));
    }
}
