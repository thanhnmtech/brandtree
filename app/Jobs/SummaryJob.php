<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\SummaryAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Job chạy ngầm: Gọi OpenAI API tóm tắt tổng hợp
 * Kích hoạt khi hoàn thành điều kiện (root/trunk/all completed)
 * Đọc prompt + input_source từ bảng summary_agents
 * Kết quả lưu vào brands.summary_data[name]
 */
class SummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Số lần retry nếu thất bại
    public int $tries = 2;

    // Timeout 120 giây (OpenAI có thể mất 10-60s)
    public int $timeout = 120;

    public int $brandId;

    // Name của summary agent: strategic_platform, authentic_foundation, consistent_identity
    public string $summaryName;

    public function __construct(int $brandId, string $summaryName)
    {
        $this->brandId = $brandId;
        $this->summaryName = $summaryName;
    }

    public function handle(): void
    {
        $brand = Brand::find($this->brandId);
        if (!$brand) {
            Log::warning("SummaryJob: Brand ID {$this->brandId} không tồn tại");
            return;
        }

        // Lấy prompt từ bảng summary_agents
        $summaryAgent = SummaryAgent::getByName($this->summaryName);
        if (!$summaryAgent || empty($summaryAgent->prompt)) {
            Log::error("SummaryJob: Summary agent '{$this->summaryName}' không tồn tại hoặc chưa có prompt");
            return;
        }

        // Xây dựng nội dung đầu vào dựa trên input_source từ DB
        $inputContent = $this->buildInputContent($brand, $summaryAgent->input_source);
        if (empty($inputContent)) {
            Log::warning("SummaryJob: Không có dữ liệu đầu vào cho '{$this->summaryName}', Brand ID: {$this->brandId}");
            return;
        }

        Log::info("SummaryJob: Bắt đầu tóm tắt '{$this->summaryName}' cho Brand ID: {$this->brandId}");
        Log::info("SummaryJob: input_source={$summaryAgent->input_source}, Prompt length: " . strlen($summaryAgent->prompt) . ", Content length: " . strlen($inputContent));

        // Gọi OpenAI API
        $summary = $this->callOpenAI($summaryAgent->prompt, $inputContent);

        if (!$summary) {
            Log::error("SummaryJob: OpenAI trả về rỗng cho '{$this->summaryName}'");
            return;
        }

        Log::info("SummaryJob: Tóm tắt thành công '{$this->summaryName}', summary length: " . strlen($summary));

        // Parse JSON/Array response từ OpenAI trước khi lưu
        $parsedResult = $this->parseJsonResponse($summary);

        // Lưu kết quả vào brands.summary_data
        $summaryData = $brand->summary_data ?? [];
        if (!is_array($summaryData)) {
            $summaryData = [];
        }
        // Lưu structured data (JSON object/array) nếu parse thành công, fallback raw string
        $summaryData[$this->summaryName] = $parsedResult;
        $brand->summary_data = $summaryData;
        $brand->save();

        Log::info("SummaryJob: Đã lưu kết quả '{$this->summaryName}' vào brands.summary_data, Brand ID: {$this->brandId}");
    }

    /**
     * Parse JSON/Array response từ OpenAI
     * OpenAI có thể trả về JSON wrapped trong markdown code block (```json...```)
     * Hoặc trả về JSON thuần
     * Fallback: trả raw string nếu không parse được
     */
    private function parseJsonResponse(string $response): mixed
    {
        $cleaned = trim($response);

        // Loại bỏ markdown code block nếu có (```json ... ``` hoặc ``` ... ```)
        if (preg_match('/^```(?:json)?\s*\n?(.*?)\n?\s*```$/s', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        }

        // Thử parse JSON
        $decoded = json_decode($cleaned, true);

        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
            Log::info("SummaryJob: Parse JSON thành công cho '{$this->summaryName}', type: " . (is_array($decoded) && array_is_list($decoded) ? 'array' : 'object'));
            return $decoded;
        }

        // Parse thất bại → log cảnh báo và trả raw string làm fallback
        Log::warning("SummaryJob: Không parse được JSON cho '{$this->summaryName}', lưu raw string. JSON error: " . json_last_error_msg());
        Log::warning("SummaryJob: Raw response: " . substr($cleaned, 0, 200));
        return $cleaned;
    }

    /**
     * Xây dựng nội dung đầu vào dựa trên input_source từ DB
     * Hỗ trợ: root, trunk, root+trunk
     */
    private function buildInputContent(Brand $brand, string $inputSource): string
    {
        $rootData = $brand->root_data ?? [];
        $trunkData = $brand->trunk_data ?? [];
        $parts = [];

        // Xác định data sources dựa trên input_source
        $includeRoot = str_contains($inputSource, 'root');
        $includeTrunk = str_contains($inputSource, 'trunk');

        if ($includeRoot) {
            foreach ($rootData as $key => $value) {
                if (!empty($value)) {
                    $parts[] = "=== {$key} ===\n{$value}";
                }
            }
        }

        if ($includeTrunk) {
            foreach ($trunkData as $key => $value) {
                if (!empty($value)) {
                    $parts[] = "=== {$key} ===\n{$value}";
                }
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * Gọi OpenAI API để tóm tắt nội dung
     * Tái sử dụng logic từ SummarizeBrandDataJob
     */
    private function callOpenAI(string $prompt, string $content): ?string
    {
        $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            Log::error('SummaryJob: OPENAI_API_KEY chưa được cấu hình');
            return null;
        }

        try {
            $data = [
                'model' => config('services.openai.chat_model', 'gpt-4o'),
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => $content],
                ],
                // Không dùng response_format: json_object vì một số prompt trả array, không phải object
                // Thay vào đó dùng parseJsonResponse() để xử lý linh hoạt
                'temperature' => 0.7, // Giảm temperature vì cần output chính xác theo format
                'max_tokens' => 2000,
            ];

            Log::info("SummaryJob: Gửi request tới OpenAI Chat Completions API cho '{$this->summaryName}'");

            $response = Http::withToken($apiKey)
                ->withOptions(['verify' => false])
                ->timeout(90)
                ->post('https://api.openai.com/v1/chat/completions', $data);

            if (!$response->successful()) {
                Log::error('SummaryJob API Error Status: ' . $response->status());
                Log::error('SummaryJob API Error Body: ' . $response->body());
                return null;
            }

            $responseData = $response->json();

            // Chuẩn OpenAI chat/completions format: choices[0].message.content
            if (isset($responseData['choices']) && is_array($responseData['choices']) && count($responseData['choices']) > 0) {
                $choice = $responseData['choices'][0];
                if (isset($choice['message']['content'])) {
                    $summary = trim($choice['message']['content']);
                    Log::info("SummaryJob: Parsed từ standard OpenAI format, length: " . strlen($summary));
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
                if (!empty($fullResponse)) {
                    Log::info("SummaryJob: Parsed từ output format, length: " . strlen($fullResponse));
                    return $fullResponse;
                }
            }

            Log::warning('SummaryJob: Không parse được response, full response: ' . json_encode($responseData));
            return null;

        } catch (\Exception $e) {
            Log::error("SummaryJob Exception: " . $e->getMessage());
            Log::error("SummaryJob Stack: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Xử lý khi job thất bại hoàn toàn
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SummaryJob FAILED cho brand {$this->brandId}, name {$this->summaryName}: " . $exception->getMessage());
    }
}
