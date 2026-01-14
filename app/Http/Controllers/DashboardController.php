<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->getBrandsData($request);

        return view('dashboard', $data);
    }

    public function filter(Request $request)
    {
        $data = $this->getBrandsData($request);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.brand-cards', ['brands' => $data['brands']])->render(),
                'stats' => [
                    'seedlingCount' => $data['seedlingCount'],
                    'growingCount' => $data['growingCount'],
                    'completedCount' => $data['completedCount'],
                    'totalBrands' => $data['totalBrands'],
                    'activeBrands' => $data['activeBrands'],
                ],
            ]);
        }

        return view('dashboard', $data);
    }

    private function getBrandsData(Request $request): array
    {
        $user = $request->user();
        $search = $request->input('search');
        $status = $request->input('status');
        $orderBy = $request->input('order_by', 'updated_at');

        $brandsQuery = $user->brands()->with('activeSubscription.plan');

        // Search filter
        if ($search) {
            $brandsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('target_market', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status && in_array($status, ['seedling', 'growing', 'completed'])) {
            $brandsQuery->where('status', $status);
        }

        // Order by
        if ($orderBy === 'created_at') {
            $brandsQuery->latest('created_at');
        } else {
            $brandsQuery->latest('updated_at');
        }

        $brands = $brandsQuery->paginate(9)->withQueryString();

        // Filter out any null brands that might exist
        $brands->getCollection()->transform(function ($brand) {
            return $brand;
        })->filter();

        // Stats
        $totalBrands = $user->brands()->count();
        $activeBrands = $user->brands()
            ->whereHas('activeSubscription')
            ->count();

        // Count by status (single query)
        $statusCounts = $user->brands()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $seedlingCount = $statusCounts['seedling'] ?? 0;
        $growingCount = $statusCounts['growing'] ?? 0;
        $completedCount = $statusCounts['completed'] ?? 0;

        return compact(
            'brands',
            'search',
            'status',
            'orderBy',
            'totalBrands',
            'activeBrands',
            'seedlingCount',
            'growingCount',
            'completedCount'
        );
    }
}
