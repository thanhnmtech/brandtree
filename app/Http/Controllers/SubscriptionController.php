<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected PlanService $planService
    ) {}

    /**
     * Show subscription details for a brand.
     */
    public function show(Brand $brand): View
    {
        $this->authorize('view', $brand);

        // Get active subscription or latest subscription (for expired case)
        $currentSubscription = $brand->activeSubscription
            ?? $brand->subscriptions()->latest('started_at')->first();

        $plans = Plan::active()
            ->subscriptions()
            ->orderBy('sort_order')
            ->get();

        return view('brands.subscription.show', compact('brand', 'currentSubscription', 'plans'));
    }

    /**
     * Show plan selection for subscribing.
     */
    public function create(Brand $brand): View
    {
        $this->authorize('update', $brand);

        $plans = Plan::active()->subscriptions()->orderBy('sort_order')->get();
        $creditPackages = Plan::active()->creditPackages()->orderBy('sort_order')->get();
        $currentSubscription = $brand->activeSubscription;

        return view('brands.subscription.create', compact(
            'brand',
            'plans',
            'creditPackages',
            'currentSubscription'
        ));
    }

    /**
     * Subscribe to a plan.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $billingCycle = $validated['billing_cycle'];

        // Check if plan is trial and brand already had trial
        if ($plan->is_trial && !$this->planService->canUseTrial($brand)) {
            return back()->withErrors(['plan_id' => __('messages.subscription.trial_used')]);
        }

        $price = $this->planService->getPlanPrice($plan, $billingCycle);

        // If plan is free/trial, activate immediately
        if ($price === 0) {
            $this->planService->activateFreeSubscription($brand, $plan, $billingCycle);

            return redirect()->route('brands.subscription.show', $brand)
                ->with('success', __('messages.subscription.activated', ['plan' => $plan->name]));
        }

        // For paid plans, create pending payment and redirect to payment page
        $payment = $this->planService->createPaymentForNewSubscription($brand, $plan, $billingCycle);

        return redirect()->route('brands.payments.show', [$brand, $payment]);
    }

    /**
     * Renew current subscription with the same plan.
     */
    public function renew(Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return back()->withErrors(['subscription' => __('messages.subscription.no_active')]);
        }

        $billingCycle = $subscription->billing_cycle ?? 'monthly';
        $price = $this->planService->getPlanPrice($subscription->plan, $billingCycle);

        // If plan is free, cannot renew (should upgrade instead)
        if ($price === 0) {
            return back()->with('error', 'Gói miễn phí không thể gia hạn. Vui lòng nâng cấp gói.');
        }

        // Create pending payment for renewal
        $payment = $this->planService->createPaymentForRenewal($brand);

        return redirect()->route('brands.payments.show', [$brand, $payment]);
    }

    /**
     * Cancel current subscription.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return back()->withErrors(['subscription' => __('messages.subscription.no_active')]);
        }

        $this->planService->cancelSubscription($subscription);

        return redirect()->route('brands.subscription.show', $brand)
            ->with('success', __('messages.subscription.cancelled'));
    }
}
