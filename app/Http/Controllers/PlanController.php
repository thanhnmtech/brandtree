<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display all available plans.
     */
    public function index(): View
    {
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

        // Credit packages
        $creditPackages = Plan::active()
            ->creditPackages()
            ->orderBy('sort_order')
            ->get();

        return view('plans.index', compact('monthlyPlans', 'yearlyPlans', 'creditPackages'));
    }
}
