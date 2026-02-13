<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandAgent;
use App\Models\AgentLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandAgentController extends Controller
{
    /**
     * Tạo tên unique cho agent, tự động thêm số (2), (3)... nếu trùng
     */
    private function generateUniqueName(int $brandId, string $baseName): string
    {
        // Kiểm tra tên gốc có tồn tại không
        $exists = BrandAgent::where('brand_id', $brandId)
            ->where('name', $baseName)
            ->exists();

        if (!$exists) {
            return $baseName;
        }

        // Tìm số lớn nhất hiện có
        $counter = 2;
        while (true) {
            $newName = "{$baseName} ({$counter})";
            $exists = BrandAgent::where('brand_id', $brandId)
                ->where('name', $newName)
                ->exists();

            if (!$exists) {
                return $newName;
            }
            $counter++;
        }
    }

    public function store(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'has_knowledge' => 'boolean'
        ]);

        // Tạo tên unique
        $uniqueName = $this->generateUniqueName($brand->id, $validated['name']);
        $code = Str::slug($uniqueName) . '-' . uniqid();

        $agent = BrandAgent::create([
            'brand_id' => $brand->id,
            'code' => $code,
            'name' => $uniqueName,
            'instruction' => $validated['instruction'] ?? null,
            'is_include' => $validated['has_knowledge'] ?? false,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Agent đã được tạo thành công.',
            'agent_id' => $agent->id, // Trả về agent_id để frontend upload file
            'redirect' => route('brands.canopy.show', $brand)
        ]);
    }

    /**
     * Tạo nhiều brand agents từ agent library
     */
    public function storeFromTemplate(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'agent_library_ids' => 'required|array|min:1',
            'agent_library_ids.*' => 'exists:agent_library,id',
        ]);

        $libraryAgents = AgentLibrary::whereIn('id', $validated['agent_library_ids'])
            ->where('is_active', true)
            ->get();

        $createdCount = 0;

        foreach ($libraryAgents as $libraryAgent) {
            // Tạo tên unique cho mỗi agent
            $uniqueName = $this->generateUniqueName($brand->id, $libraryAgent->name);

            BrandAgent::create([
                'brand_id' => $brand->id,
                'code' => $libraryAgent->code . '-' . uniqid(),
                'name' => $uniqueName,
                'instruction' => $libraryAgent->instruction,
                'prompt' => $libraryAgent->prompt ?? null,
                'vector_id' => $libraryAgent->vector_id ?? null,
                'is_include' => true,
                'created_by' => auth()->id(),
            ]);
            $createdCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã tạo thành công {$createdCount} Agent.",
            'redirect' => route('brands.canopy.show', $brand)
        ]);
    }

    /**
     * Xóa brand agent
     */
    public function destroy(Brand $brand, BrandAgent $agent)
    {
        // Kiểm tra agent thuộc về brand này
        if ($agent->brand_id !== $brand->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent không thuộc thương hiệu này.'
            ], 403);
        }

        $agent->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa Agent thành công.'
        ]);
    }

    /**
     * Cập nhật thông tin brand agent
     */
    public function update(Request $request, Brand $brand, BrandAgent $agent)
    {
        // Kiểm tra agent thuộc về brand này
        if ($agent->brand_id !== $brand->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent không thuộc thương hiệu này.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'has_knowledge' => 'boolean'
        ]);

        // Kiểm tra trùng tên (trừ chính nó)
        $existingName = BrandAgent::where('brand_id', $brand->id)
            ->where('name', $validated['name'])
            ->where('id', '!=', $agent->id)
            ->exists();

        if ($existingName) {
            // Tạo tên unique
            $validated['name'] = $this->generateUniqueName($brand->id, $validated['name']);
        }

        $agent->update([
            'name' => $validated['name'],
            'instruction' => $validated['instruction'] ?? null,
            'is_include' => $validated['has_knowledge'] ?? false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã cập nhật Agent thành công.',
            'agent' => $agent
        ]);
    }
}
