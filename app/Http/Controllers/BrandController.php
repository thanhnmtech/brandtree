<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandMember;
use App\Models\Plan;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\RedirectResponse;
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
    public function store(StoreBrandRequest $request): RedirectResponse
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
                'credits_reset_at' => now(),
                'status' => 'active',
            ]);
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
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): View
    {
        $this->authorize('update', $brand);

        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
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

        return redirect()->route('brands.show', $brand)
            ->with('success', __('messages.brand.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->authorize('delete', $brand);

        // Delete logo if exists
        if ($brand->logo_path) {
            Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', __('messages.brand.deleted'));
    }
}
