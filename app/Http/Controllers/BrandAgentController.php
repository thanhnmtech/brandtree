<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandAgentController extends Controller
{
    public function store(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'has_knowledge' => 'boolean' // Just for logic if needed, but per request we just store inputs
        ]);

        $code = Str::slug($validated['name']) . '-' . uniqid();

        $agent = BrandAgent::create([
            'brand_id' => $brand->id,
            'code' => $code, // Unique code
            'name' => $validated['name'],
            'instruction' => $validated['instruction'] ?? null,
            'is_include' => $validated['has_knowledge'] ?? false,
            'created_by' => auth()->id(),
            // 'vector_id' =>  null // Not provided in form yet
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Agent đã được tạo thành công.',
            'redirect' => route('brands.canopy.show', $brand) // Reload page essentially
        ]);
    }
}
