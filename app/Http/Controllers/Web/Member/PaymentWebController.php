<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentWebController extends Controller
{
    public function __invoke(Request $request): View
    {
        $payments = $request->user()
            ->payments()
            ->with('subscription.plan')
            ->latest()
            ->paginate(15);

        return view('member.payments', compact('payments'));
    }
}
