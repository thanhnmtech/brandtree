<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\AgentSystem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Job chạy ngầm: Gọi OpenAI API tóm tắt nội dung phân tích
 * và lưu kết quả vào root_brief_data / trunk_brief_data
 */
class SummarizeBrandDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Số lần retry nếu thất bại
    public int $tries = 2;

    // Timeout 120 giây (OpenAI có thể mất 10-60s)
    public int $timeout = 120;

    public int $brandId;
    public string $agentType;
    public string $content;

    public function __construct(int $brandId, string $agentType, string $content)
    {
        $this->brandId = $brandId;
        $this->agentType = $agentType;
        $this->content = $content;
    }

    public function handle(): void
    {
        $brand = Brand::find($this->brandId);
        if (!$brand) {
            Log::warning("SummarizeBrandDataJob: Brand ID {$this->brandId} không tồn tại");
            return;
        }

        $agent = AgentSystem::where('agent_type', $this->agentType)->first();
        if (!$agent || empty($agent->brief_prompt)) {
            Log::warning("SummarizeBrandDataJob: Agent {$this->agentType} không có brief_prompt");
            return;
        }

        // Gọi OpenAI API tóm tắt
        $summary = $this->callOpenAI($agent->brief_prompt, $this->content);

        if (!$summary) {
            Log::warning("SummarizeBrandDataJob: OpenAI trả về rỗng cho {$this->agentType}");
            return;
        }

        // Xác định cột lưu brief data
        $rootTypes = ['root1', 'root2', 'root3'];
        $briefColumn = in_array($this->agentType, $rootTypes) ? 'root_brief_data' : 'trunk_brief_data';

        // Lưu vào DB
        $briefData = $brand->$briefColumn ?? [];
        if (!is_array($briefData)) {
            $briefData = (array) $briefData;
        }
        $briefData[$this->agentType] = $summary;
        $brand->$briefColumn = $briefData;
        $brand->save();
    }

    /**
     * Gọi OpenAI API để tóm tắt nội dung
     */
    private function callOpenAI(string $briefPrompt, string $content): ?string
    {
        $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            Log::warning('SummarizeBrandDataJob: OPENAI_API_KEY chưa được cấu hình');
            return null;
        }

        try {
            $data = [
                'model' => config('services.openai.chat_model', 'gpt-4o'),
                'instructions' => $briefPrompt,
                'input' => $content,
                'stream' => false,
            ];

            $response = Http::withToken($apiKey)
                ->withOptions(['verify' => false])
                ->timeout(60)
                ->post('https://api.openai.com/v1/responses', $data);

            if (!$response->successful()) {
                Log::error('SummarizeBrandDataJob API Error: ' . $response->body());
                return null;
            }

            $responseData = $response->json();

            // Parse kết quả: $responseData['output'][0]['content'][0]['text']
            if (isset($responseData['output']) && is_array($responseData['output'])) {
                $fullResponse = '';
                foreach ($responseData['output'] as $outputItem) {
                    if (isset($outputItem['content']) && is_array($outputItem['content'])) {
                        foreach ($outputItem['content'] as $contentItem) {
                            if (isset($contentItem['type']) && $contentItem['type'] === 'output_text' && isset($contentItem['text'])) {
                                $fullResponse .= $contentItem['text'];
                            }
                        }
                    }
                }
                if (!empty($fullResponse)) {
                    return $fullResponse;
                }
            }

            // Fallback: SSE response.completed format
            if (isset($responseData['response']['output'])) {
                $fullResponse = '';
                foreach ($responseData['response']['output'] as $outputItem) {
                    if (isset($outputItem['content']) && is_array($outputItem['content'])) {
                        foreach ($outputItem['content'] as $contentItem) {
                            if (isset($contentItem['type']) && $contentItem['type'] === 'output_text' && isset($contentItem['text'])) {
                                $fullResponse .= $contentItem['text'];
                            }
                        }
                    }
                }
                if (!empty($fullResponse)) {
                    return $fullResponse;
                }
            }

            // Fallback: Chuẩn OpenAI gốc
            if (isset($responseData['choices'][0]['message']['content'])) {
                return $responseData['choices'][0]['message']['content'];
            }

            Log::warning('SummarizeBrandDataJob: Không parse được response: ' . json_encode($responseData));
            return null;

        } catch (\Exception $e) {
            Log::error('SummarizeBrandDataJob Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Xử lý khi job thất bại hoàn toàn
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SummarizeBrandDataJob FAILED cho brand {$this->brandId}, agent {$this->agentType}: " . $exception->getMessage());
    }
}
