<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BrandDataController extends Controller
{
    public function store(Request $request, Brand $brand)
    {
        // 1. Validate inputs
        $validated = $request->validate([
            'agentType' => 'required|string',
            'content' => 'required|string',
        ]);

        $agentType = $validated['agentType'];
        $rawContent = $validated['content'];

        // 2. Determine target column and key
        $targetColumn = null;
        $jsonKey = $agentType; // e.g., root1, trunk2

        $rootTypes = ['root1', 'root2', 'root3'];
        $trunkTypes = ['trunk1', 'trunk2'];

        if (in_array($agentType, $rootTypes)) {
            $targetColumn = 'root_data';
        } elseif (in_array($agentType, $trunkTypes)) {
            $targetColumn = 'trunk_data';
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid agentType'
            ], 400);
        }

        // 3. Process Content with OpenAI (GPT-4o-mini)
        $apiKey = env('OPENAI_API_KEY');
        $refinedContent = $rawContent; // Default to raw if AI fails

        try {
            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Bạn sẽ đọc nội dung tin nhắn, và chỉ giữ lại mô tả về thông tin thương hiệu, loại bỏ các câu chào hỏi, hỏi thăm, lời chúc, hay hỏi làm thêm gì khác v.v... Trả lời chỉ có nội dung thông tin về thương hiệu, không thêm bất cứ nội dung nào khác.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Hãy trích xuất nội dung liên quan thương hiệu trong tin nhắn này: \n" . $rawContent
                    ]
                ],
                'temperature' => 0.3
            ]);

            if ($response->successful()) {
                $refinedContent = $response->json()['choices'][0]['message']['content'] ?? $rawContent;
                // Trim potential extra whitespace
                $refinedContent = trim($refinedContent);
            } else {
                Log::error('OpenAI content refinement failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI content refinement exception', ['message' => $e->getMessage()]);
        }

        // 4. Update JSON data
        // Laravel attribute casting handles JSON array automatically.
        // We interact with the array directly.

        $currentData = $brand->$targetColumn ?? [];

        // Ensure structure exists
        if ($targetColumn === 'root_data') {
            $defaultStructure = ['root1' => '', 'root2' => '', 'root3' => ''];
            // Merge defaults with current to ensure keys exist, but don't overwrite existing data
            $currentData = array_merge($defaultStructure, $currentData);
        } elseif ($targetColumn === 'trunk_data') {
            $defaultStructure = ['trunk1' => '', 'trunk2' => ''];
            $currentData = array_merge($defaultStructure, $currentData);
        }

        // Update the specific value with REFINED content
        $currentData[$jsonKey] = $refinedContent;

        // Save back
        $brand->$targetColumn = $currentData;
        $brand->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu thành công vào thương hiệu.'
        ]);
    }
    /**
     * Update specific section content directly (No AI Refinement)
     */
    public function updateSection(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $key = $validated['key'];
        $content = $validated['content'] ?? '';

        // Determine column based on key
        $targetColumn = null;
        if (in_array($key, ['root1', 'root2', 'root3'])) {
            $targetColumn = 'root_data';
        } elseif (in_array($key, ['trunk1', 'trunk2'])) {
            $targetColumn = 'trunk_data';
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid key'], 400);
        }

        // Get current data using array access (since we added casts in Model)
        $currentData = $brand->$targetColumn ?? [];

        // Update value
        $currentData[$key] = $content;

        // Save
        $brand->$targetColumn = $currentData;
        $brand->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu thành công.'
        ]);
    }
}
