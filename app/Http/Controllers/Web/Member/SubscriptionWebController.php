<?php

namespace App\Http\Controllers\Web\Member;

use App\Exceptions\SubscriptionException;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\SubscriptionServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionWebController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService,
        private readonly PaymentServiceInterface $paymentService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $activeSubscription = $user->activeSubscription()->with('plan')->first();
        $plans              = SubscriptionPlan::active()->get();
        $subscriptions      = $user->subscriptions()->with(['plan', 'payment'])->latest()->paginate(10);

        return view('member.subscriptions', compact('activeSubscription', 'plans', 'subscriptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_id' => ['required', 'uuid', 'exists:subscription_plans,id'],
            'method'  => ['required', 'in:wave,orange_money'],
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        try {
            $subscription = $this->subscriptionService->create($request->user(), $plan);
            $this->paymentService->initiate($subscription, $request->input('method'));
            return redirect()->route('member.subscriptions')
                ->with('success', 'Abonnement créé — validez le paiement ci-dessous.');
        } catch (SubscriptionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
