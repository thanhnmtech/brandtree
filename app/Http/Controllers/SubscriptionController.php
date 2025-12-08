<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandSubscription;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Show subscription details for a brand.
     */
    public function show(Brand $brand): View
    {
        $this->authorize('view', $brand);

        $subscription = $brand->activeSubscription;
        $plans = Plan::active()->orderBy('sort_order')->get();

        // Monthly subscription plans
        $monthlyPlans = Plan::active()
            ->subscriptions()
            ->monthly()
            ->orderBy('sort_order')
            ->get();

        // Yearly subscription plans
        $yearlyPlans = Plan::active()
            ->subscriptions()
            ->yearly()
            ->orderBy('sort_order')
            ->get();

         $currentSubscription = $brand->activeSubscription;

        return view('brands.subscription.show', compact('brand', 'subscription', 'plans', 'monthlyPlans', 'yearlyPlans', 'currentSubscription'));
    }

    /**
     * Show plan selection for subscribing.
     */
    public function create(Brand $brand): View
    {
        $this->authorize('update', $brand);

        $monthlyPlans = Plan::active()->subscriptions()->monthly()->orderBy('sort_order')->get();
        $yearlyPlans = Plan::active()->subscriptions()->yearly()->orderBy('sort_order')->get();
        $creditPackages = Plan::active()->creditPackages()->orderBy('sort_order')->get();
        $currentSubscription = $brand->activeSubscription;

        return view('brands.subscription.create', compact(
            'brand',
            'monthlyPlans',
            'yearlyPlans',
            'creditPackages',
            'currentSubscription'
        ));
    }

    /**
     * Subscribe to a plan (creates pending subscription).
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        // Check if plan is trial and brand already had trial
        if ($plan->is_trial) {
            $hadTrial = $brand->subscriptions()->whereHas('plan', function ($q) {
                $q->where('is_trial', true);
            })->exists();

            if ($hadTrial) {
                return back()->withErrors(['plan_id' => __('messages.subscription.trial_used')]);
            }
        }

        // If plan is free/trial, activate immediately
        if ($plan->price === 0) {
            // Expire current subscription if exists
            if ($brand->activeSubscription) {
                $brand->activeSubscription->update(['status' => BrandSubscription::STATUS_EXPIRED]);
            }

            BrandSubscription::create([
                'brand_id' => $brand->id,
                'plan_id' => $plan->id,
                'started_at' => now(),
                'expires_at' => now()->addDays($plan->duration_days),
                'credits_remaining' => $plan->credits,
                'credits_reset_at' => now(),
                'status' => BrandSubscription::STATUS_ACTIVE,
            ]);

            return redirect()->route('brands.subscription.show', $brand)
                ->with('success', __('messages.subscription.activated', ['plan' => $plan->name]));
        }

        // For paid plans, create pending subscription and redirect to payment
        $subscription = BrandSubscription::create([
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'started_at' => now(), // Will be updated to actual start time after payment
            'expires_at' => now()->addDays($plan->duration_days),
            'credits_remaining' => 0,
            'credits_reset_at' => now(),
            'status' => BrandSubscription::STATUS_PENDING,
        ]);

        // Redirect to payment page
        return redirect()->route('brands.payments.create', [$brand, 'subscription' => $subscription->id]);
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

        $subscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);

        return redirect()->route('brands.subscription.show', $brand)
            ->with('success', __('messages.subscription.cancelled'));
    }
}
