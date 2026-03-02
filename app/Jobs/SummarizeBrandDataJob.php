<?php

namespace App\Jobs;

use App\Models\AgentSystem;
use App\Models\Brand;
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
        if (! $brand) {
            Log::warning("SummarizeBrandDataJob: Brand ID {$this->brandId} không tồn tại");

            return;
        }

        $agent = AgentSystem::where('agent_type', $this->agentType)->first();
        if (! $agent || empty($agent->brief_prompt)) {
            Log::error("SummarizeBrandDataJob: Agent {$this->agentType} không có brief_prompt hoặc agent không tồn tại");
            Log::error("Agent data: " . json_encode($agent ? $agent->toArray() : 'NULL'));

            return;
        }

        Log::info("SummarizeBrandDataJob: Bắt đầu tóm tắt cho {$this->agentType}, Brand ID: {$this->brandId}");
        Log::info("Brief Prompt: " . substr($agent->brief_prompt, 0, 100) . "...");
        Log::info("Content length: " . strlen($this->content));

        // Gọi OpenAI API tóm tắt
        $summary = $this->callOpenAI($agent->brief_prompt, $this->content);

        if (! $summary) {
            Log::error("SummarizeBrandDataJob: OpenAI trả về rỗng cho {$this->agentType}");

            return;
        }

        Log::info("SummarizeBrandDataJob: Tóm tắt thành công, summary length: " . strlen($summary));

        // Xác định cột lưu brief data
        $rootTypes = ['root1', 'root2', 'root3'];
        $briefColumn = in_array($this->agentType, $rootTypes) ? 'root_brief_data' : 'trunk_brief_data';

        // Lưu vào DB
        $briefData = $brand->$briefColumn ?? [];
        if (! is_array($briefData)) {
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
            Log::error('SummarizeBrandDataJob: OPENAI_API_KEY chưa được cấu hình');

            return null;
        }

        try {
            $data = [
                'model' => config('services.openai.chat_model', 'gpt-4o'),
                'messages' => [
                    ['role' => 'system', 'content' => $briefPrompt],
                    ['role' => 'user', 'content' => $content],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ];

            Log::info("SummarizeBrandDataJob: Gửi request tới OpenAI Chat Completions API");

            $response = Http::withToken($apiKey)
                ->withOptions(['verify' => false])
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', $data);

            if (! $response->successful()) {
                Log::error('SummarizeBrandDataJob API Error Status: ' . $response->status());
                Log::error('SummarizeBrandDataJob API Error Body: ' . $response->body());

                return null;
            }

            $responseData = $response->json();
            Log::info('SummarizeBrandDataJob Response Keys: ' . json_encode(array_keys($responseData)));

            // Chuẩn OpenAI chat/completions format: choices[0].message.content
            if (isset($responseData['choices']) && is_array($responseData['choices']) && count($responseData['choices']) > 0) {
                $choice = $responseData['choices'][0];
                if (isset($choice['message']['content'])) {
                    $summary = trim($choice['message']['content']);
                    Log::info("SummarizeBrandDataJob: Parsed từ standard OpenAI format, length: " . strlen($summary));
                    return $summary;
                }
            }

            // Fallback: output array format (non-standard)
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
                if (! empty($fullResponse)) {
                    Log::info("SummarizeBrandDataJob: Parsed từ output format, length: " . strlen($fullResponse));
                    return $fullResponse;
                }
            }

            Log::warning('SummarizeBrandDataJob: Không parse được response, full response: ' . json_encode($responseData));

            return null;

        } catch (\Exception $e) {
            Log::error('SummarizeBrandDataJob Exception: ' . $e->getMessage());
            Log::error('SummarizeBrandDataJob Stack: ' . $e->getTraceAsString());

            return null;
        }
    }

    /**
     * Xử lý khi job thất bại hoàn toàn
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SummarizeBrandDataJob FAILED cho brand {$this->brandId}, agent {$this->agentType}: ".$exception->getMessage());
    }
}
