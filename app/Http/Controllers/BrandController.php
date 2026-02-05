<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandAgent;
use App\Models\BrandMember;
use App\Models\AgentLibrary;
use App\Models\Plan;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Brand::class);

        $brands = auth()->user()->brands()->with('activeSubscription.plan')->latest()->get();

        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Brand::class);

        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Brand::class);
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('brands/logos', 'public');
        }

        $data['created_by'] = auth()->id();

        // Create brand
        $brand = Brand::create($data);

        // Add creator as admin member
        BrandMember::create([
            'brand_id' => $brand->id,
            'user_id' => auth()->id(),
            'role' => BrandMember::ROLE_ADMIN,
            'joined_at' => now(),
        ]);

        // Create trial subscription
        $trialPlan = Plan::where('is_trial', true)->first();
        if ($trialPlan) {
            $brand->subscriptions()->create([
                'plan_id' => $trialPlan->id,
                'started_at' => now(),
                'expires_at' => now()->addDays($trialPlan->duration_days),
                'credits_remaining' => $trialPlan->credits,
                'credits_reset_at' => now()->addMonth(),
                'status' => 'active',
            ]);
        }

        // Tự động tạo Brand Agents từ Agent Library
        $activeLibraryAgents = AgentLibrary::where('is_active', true)->get();
        foreach ($activeLibraryAgents as $libraryAgent) {
            BrandAgent::create([
                'brand_id' => $brand->id,
                'code' => $libraryAgent->code . '-' . uniqid(),
                'name' => $libraryAgent->name,
                'instruction' => $libraryAgent->instruction,
                'prompt' => $libraryAgent->prompt ?? null,
                'vector_id' => $libraryAgent->vector_id ?? null,
                'is_include' => true,
                'created_by' => auth()->id(),
            ]);
        }

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.brand.created'),
                'brand' => $brand->load('activeSubscription.plan'),
                'redirect' => route('brands.show', $brand)
            ], 201);
        }

        return redirect()->route('brands.show', $brand)
            ->with('success', __('messages.brand.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): View
    {
        $this->authorize('view', $brand);

        $brand->load(['members.user', 'activeSubscription.plan']);

        // Tính toán trạng thái các phase cho Progress Cards
        $phases = $this->calculatePhaseStatuses($brand);

        return view('brands.show', compact('brand', 'phases'));
    }

    /**
     * Tính toán trạng thái của 3 phase: Root, Trunk, Canopy
     * Lấy danh sách steps từ config/timeline_steps.php
     * 
     * @param Brand $brand
     * @return array
     */
    private function calculatePhaseStatuses(Brand $brand): array
    {
        $rootData = $brand->root_data ?? [];
        $trunkData = $brand->trunk_data ?? [];

        // Lấy danh sách steps từ config (dynamic)
        $rootSteps = array_keys(config('timeline_steps.root', []));
        $trunkSteps = array_keys(config('timeline_steps.trunk', []));

        $rootTotal = count($rootSteps);
        $trunkTotal = count($trunkSteps);

        // Đếm số steps đã hoàn thành
        $rootCompleted = count(array_filter($rootSteps, fn($k) => !empty($rootData[$k])));
        $trunkCompleted = count(array_filter($trunkSteps, fn($k) => !empty($trunkData[$k])));

        $isRootFinished = $rootTotal > 0 && $rootCompleted === $rootTotal;
        $isTrunkFinished = $trunkTotal > 0 && $trunkCompleted === $trunkTotal;

        // Xác định trạng thái và URL cho từng phase
        return [
            'root' => [
                'status' => $isRootFinished ? 'completed' : 'ready',
                'progress' => $rootTotal > 0 ? round(($rootCompleted / $rootTotal) * 100) : 0,
                'url' => $isRootFinished ? null : route('chat', [
                    'brand' => $brand->slug, 
                    'agentType' => $this->getNextStep($rootData, $rootSteps)
                ]),
            ],
            'trunk' => [
                'status' => $isTrunkFinished ? 'completed' : ($isRootFinished ? 'ready' : 'locked'),
                'progress' => $isRootFinished && $trunkTotal > 0 ? round(($trunkCompleted / $trunkTotal) * 100) : 0,
                'url' => ($isRootFinished && !$isTrunkFinished) 
                    ? route('chat', [
                        'brand' => $brand->slug, 
                        'agentType' => $this->getNextStep($trunkData, $trunkSteps)
                    ]) 
                    : null,
            ],
            'canopy' => [
                'status' => ($isRootFinished && $isTrunkFinished) ? 'ready' : 'locked',
                'progress' => 0, // Canopy không có progress bar
                'url' => ($isRootFinished && $isTrunkFinished) 
                    ? route('brands.canopy.show', $brand) 
                    : null,
            ],
        ];
    }


    /**
     * Lấy step tiếp theo cần hoàn thành
     * 
     * @param array $data Dữ liệu hiện tại (root_data hoặc trunk_data)
     * @param array $steps Danh sách steps cần hoàn thành
     * @return string
     */
    private function getNextStep(array $data, array $steps): string
    {
        foreach ($steps as $step) {
            if (empty($data[$step])) {
                return $step;
            }
        }
        return $steps[0]; // Fallback
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $brand);
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('brands/logos', 'public');
        }

        $brand->update($data);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.brand.updated'),
                'brand' => $brand->fresh()->load('activeSubscription.plan'),
                'redirect' => route('brands.show', $brand->fresh())
            ]);
        }

        return redirect()->route('brands.show', $brand)
            ->with('success', __('messages.brand.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $brand);

        // Soft delete the brand (logo will be kept for recovery)
        $brand->delete();

        // Return JSON response for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.brand.deleted'),
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', __('messages.brand.deleted'));
    }
}
