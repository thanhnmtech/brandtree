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

        return view('brands.show', compact('brand'));
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
