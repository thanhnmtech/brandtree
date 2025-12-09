<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CreditUsage;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreditController extends Controller
{
    public function __construct(
        protected CreditService $creditService
    ) {}

    /**
     * Display credit usage history for a brand.
     */
    public function index(Request $request, Brand $brand): View
    {
        $this->authorize('view', $brand);

        $subscription = $brand->activeSubscription;
        $period = $request->get('period', '30days');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get usage history with filters
        $query = CreditUsage::where('brand_id', $brand->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter by period
        if ($period === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($period === '60days') {
            $query->where('created_at', '>=', now()->subDays(60));
        }
        // 'all' = no filter

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action_type', $request->get('action'));
        }

        $usages = $query->paginate(20)->withQueryString();

        // Get stats
        $stats = $this->creditService->getUsageStats($brand, $period, $startDate, $endDate);

        // Get daily usage for chart
        $dailyUsage = $this->creditService->getDailyUsage($brand, $period, $startDate, $endDate);

        return view('brands.credits.index', compact('brand', 'subscription', 'usages', 'stats', 'period', 'dailyUsage'));
    }

    /**
     * Export credit usage to CSV.
     */
    // public function export(Request $request, Brand $brand): StreamedResponse
    // {
    //     $this->authorize('view', $brand);

    //     $period = $request->get('period', 'month');

    //     $query = CreditUsage::where('brand_id', $brand->id)
    //         ->with('user')
    //         ->orderBy('created_at', 'desc');

    //     if ($period === 'week') {
    //         $query->where('created_at', '>=', now()->startOfWeek());
    //     } elseif ($period === 'month') {
    //         $query->where('created_at', '>=', now()->startOfMonth());
    //     } elseif ($period === 'year') {
    //         $query->where('created_at', '>=', now()->startOfYear());
    //     }

    //     $usages = $query->get();

    //     $filename = "credits-{$brand->id}-{$period}-" . now()->format('Y-m-d') . ".csv";

    //     return response()->streamDownload(function () use ($usages) {
    //         $handle = fopen('php://output', 'w');

    //         // Header
    //         fputcsv($handle, ['Thời gian', 'Người dùng', 'Loại', 'Model', 'Credits', 'Mô tả']);

    //         // Data
    //         foreach ($usages as $usage) {
    //             fputcsv($handle, [
    //                 $usage->created_at->format('d/m/Y H:i:s'),
    //                 $usage->user->name,
    //                 $usage->action_type,
    //                 $usage->model_used ?? '',
    //                 $usage->amount,
    //                 $usage->description ?? '',
    //             ]);
    //         }

    //         fclose($handle);
    //     }, $filename, [
    //         'Content-Type' => 'text/csv',
    //     ]);
    // }
}
