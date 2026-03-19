<?php

namespace App\Http\Controllers;

use App\Jobs\SummarizeBrandDataJob;
use App\Jobs\SummaryJob;
use App\Models\Brand;
use App\Models\SummaryAgent;
use Illuminate\Http\Request;

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
                'message' => 'Invalid agentType',
            ], 400);
        }

        // 3. Process Content (Skipped AI Refinement)
        $refinedContent = $rawContent;

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

        // Xóa bản tóm tắt cũ nếu người dùng gửi content rỗng
        if (empty(trim($refinedContent ?? ''))) {
            $prefix = in_array($agentType, $rootTypes) ? 'root' : 'trunk';
            $briefColumn = "{$prefix}_brief_data";
            $currentBriefData = $brand->$briefColumn ?? [];
            if (!is_array($currentBriefData)) $currentBriefData = [];
            
            $currentBriefData[$jsonKey] = ''; 
            $brand->$briefColumn = $currentBriefData;
        }

        $brand->save();

        // Tự động cập nhật status dựa trên dữ liệu root/trunk
        $brand->updateStatusFromData();

        // Dispatch Job chạy ngầm để gọi OpenAI tóm tắt từng section
        if (!empty(trim($refinedContent ?? ''))) {
            SummarizeBrandDataJob::dispatch($brand->id, $agentType, $refinedContent);
        }

        // Dispatch các SummaryJob tổng hợp nếu đủ điều kiện
        $brand->refresh(); // Refresh để lấy data mới nhất
        $this->dispatchSummaryJobs($brand, $agentType);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu thành công vào thương hiệu.',
        ]);
    }

    /**
     * Update specific section content directly (No AI Refinement)
     */
    public function updateSection_legacy(Request $request, Brand $brand)
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

        // Lưu nội dung phân tích
        $brand->$targetColumn = $currentData;

        // Re-parse content thành keyword items
        $parsedItems = [];
        if (! empty($content)) {
            $parsedItems = \App\Services\BrandContentParser::parseContent($key, $content);
            $itemsColumn = "{$key}_data_items";
            $brand->$itemsColumn = $parsedItems;
        }

        $brand->save();

        // Dispatch Job chạy ngầm để gọi OpenAI tóm tắt
        if (! empty($content)) {
            SummarizeBrandDataJob::dispatch($brand->id, $key, $content);
        }

        // Tính toán lại trạng thái phases để cập nhật giao diện
        $phases = $brand->calculatePhaseStatuses();

        // Render HTML cho phần Next Step
        $nextStepHtml = view('brands.partials.next-step', [
            'brand' => $brand,
            'phases' => $phases,
        ])->render();

        // Render HTML cho phần Progress Header (các card tiến trình)
        $progressHeaderHtml = view('brands.partials.progress-header', [
            'brand' => $brand,
            'phases' => $phases,
        ])->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu thành công.',
            'data_items' => $parsedItems,
            'next_step_html' => $nextStepHtml,
            'progress_header_html' => $progressHeaderHtml,
        ]);
    }

    public function updateSection(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'content' => 'nullable|string',
            'type' => 'required|in:data,brief',
        ]);

        $key = $validated['key'];
        $content = $validated['content'] ?? '';
        $type = $validated['type'];

        // Xác định prefix (root / trunk)
        if (str_starts_with($key, 'root')) {
            $prefix = 'root';
        } elseif (str_starts_with($key, 'trunk')) {
            $prefix = 'trunk';
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid key',
            ], 400);
        }

        // Xác định column cần update
        $targetColumn = $type === 'brief'
            ? "{$prefix}_brief_data"
            : "{$prefix}_data";

        // Lấy dữ liệu hiện tại
        $currentData = $brand->$targetColumn;

        if (!is_array($currentData)) {
            $currentData = [];
        }

        // Update section
        $currentData[$key] = $content;

        // Save
        $brand->$targetColumn = $currentData;

        // Xóa bản tóm tắt cũ nếu người dùng gửi content rỗng
        if ($type === 'data' && empty(trim($content ?? ''))) {
            $briefColumn = "{$prefix}_brief_data";
            $currentBriefData = $brand->$briefColumn ?? [];
            if (!is_array($currentBriefData)) $currentBriefData = [];
            
            $currentBriefData[$key] = ''; 
            $brand->$briefColumn = $currentBriefData;
        }

        $brand->save();

        // Tự động cập nhật status dựa trên dữ liệu root/trunk
        $brand->updateStatusFromData();

        // Dispatch job nếu là data (không phải brief)
        if ($type === 'data' && !empty(trim($content ?? ''))) {
            SummarizeBrandDataJob::dispatch($brand->id, $key, $content);
        }

        // Dispatch các SummaryJob tổng hợp nếu đủ điều kiện (chỉ khi lưu data)
        if ($type === 'data') {
            $brand->refresh();
            $this->dispatchSummaryJobs($brand, $key);
        }

        // Update phase status
        $phases = $brand->calculatePhaseStatuses();

        // Render partials
        $nextStepHtml = view('brands.partials.next-step', [
            'brand' => $brand,
            'phases' => $phases,
        ])->render();

        $progressHeaderHtml = view('brands.partials.progress-header', [
            'brand' => $brand,
            'phases' => $phases,
        ])->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu thành công.',
            'next_step_html' => $nextStepHtml,
            'progress_header_html' => $progressHeaderHtml,
        ]);
    }

    /**
     * API endpoint cho frontend polling kiểm tra brief data đã sẵn sàng chưa
     */
    public function getBriefStatus(Request $request, Brand $brand)
    {
        $key = $request->query('key');
        if (! $key) {
            return response()->json(['ready' => false]);
        }

        // Xác định cột brief
        $rootTypes = ['root1', 'root2', 'root3'];
        $briefColumn = in_array($key, $rootTypes) ? 'root_brief_data' : 'trunk_brief_data';

        $briefData = $brand->$briefColumn ?? [];
        $content = $briefData[$key] ?? null;

        $parsedContent = app(\App\Services\BriefContentParser::class)->parse($key, $content);

        return response()->json([
            'ready' => ! empty($content),
            'content' => $content,
            'parsed_content' => $parsedContent,
        ]);
    }

    /**
     * Dispatch các SummaryJob tổng hợp dựa trên điều kiện hoàn thành
     * Logic generic: đọc trigger_condition từ DB, không hardcode tên prompt
     * → Khi thêm prompt mới chỉ cần insert record vào summary_agents
     */
    private function dispatchSummaryJobs(Brand $brand, string $agentType): void
    {
        $isRoot = str_starts_with($agentType, 'root');
        $isTrunk = str_starts_with($agentType, 'trunk');

        // Lấy tất cả summary agents đang active
        $summaryAgents = SummaryAgent::where('is_active', true)->get();

        foreach ($summaryAgents as $agent) {
            $shouldDispatch = false;

            switch ($agent->trigger_condition) {
                case SummaryAgent::TRIGGER_ROOT_COMPLETED:
                    // Chỉ dispatch khi vừa cập nhật root VÀ root đã hoàn thành
                    $shouldDispatch = $isRoot && $brand->isRootCompleted();
                    break;

                case SummaryAgent::TRIGGER_TRUNK_COMPLETED:
                    // Chỉ dispatch khi vừa cập nhật trunk VÀ trunk đã hoàn thành
                    $shouldDispatch = $isTrunk && $brand->isTrunkCompleted();
                    break;

                case SummaryAgent::TRIGGER_ALL_COMPLETED:
                    // Dispatch khi cả root + trunk đều hoàn thành
                    $shouldDispatch = $brand->isAllStepsCompleted();
                    break;
            }

            if ($shouldDispatch) {
                SummaryJob::dispatch($brand->id, $agent->name);
            }
        }
    }

    /**
     * API endpoint cho frontend polling kiểm tra summary data đã sẵn sàng chưa
     */
    public function getSummaryStatus(Request $request, Brand $brand)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(['ready' => false]);
        }

        $summaryData = $brand->summary_data ?? [];
        $content = $summaryData[$name] ?? null;

        $parsedContent = null;
        if ($content) {
            if (is_array($content)) {
                $parsedContent = $content;
            } elseif (is_string($content)) {
                // Normalize smart quotes
                $cleaned = str_replace(
                    ["\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}"],
                    ['"', '"', "'", "'"],
                    trim($content)
                );
                // Loại bỏ markdown code block
                if (preg_match('/^```(?:json)?\s*\n?(.*?)\n?\s*```$/s', $cleaned, $m)) {
                    $cleaned = trim($m[1]);
                }
                $decoded = json_decode($cleaned, true);
                if (json_last_error() === JSON_ERROR_NONE && $decoded) {
                    $parsedContent = $decoded;
                }
            }
        }

        return response()->json([
            'ready' => !empty($content),
            'content' => $parsedContent ?: $content,
        ]);
    }
}

