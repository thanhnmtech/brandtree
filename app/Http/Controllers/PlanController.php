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
        $plans = Plan::active()
            ->orderBy('sort_order')
            ->get();

        return view('plans.index', compact('plans'));
    }
}
