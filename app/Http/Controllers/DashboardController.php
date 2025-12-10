<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->input('search');

        $brandsQuery = $user->brands()->with('activeSubscription.plan');

        // Search filter
        if ($search) {
            $brandsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('target_market', 'like', "%{$search}%");
            });
        }

        $brands = $brandsQuery->latest()->paginate(9)->withQueryString();

        // Filter out any null brands that might exist
        $brands->getCollection()->transform(function ($brand) {
            return $brand;
        })->filter();

        // Stats
        $totalBrands = $user->brands()->count();
        $activeBrands = $user->brands()
            ->whereHas('activeSubscription')
            ->count();

        return view('dashboard', compact('brands', 'search', 'totalBrands', 'activeBrands'));
    }
}
