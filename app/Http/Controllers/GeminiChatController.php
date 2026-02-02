<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\AgentSystem;
use App\Models\BrandAgent;
use App\Models\Brand;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GeminiChatController extends Controller
{
    public function stream(Request $request)
    {
        $convId = $request->input('convId');
        $userInput = $request->input('message');
        $agentType = $request->input('agentType');
        $agentId = $request->input('agentId');
        $brandId = $request->input('brandId');
        $userId = $request->user() ? $request->user()->id : null;

        // --- Prompt Building Logic (Copied from ChatStreamController) ---
        $prompt = "Bạn là trợ lý ảo.";

        if ($agentType === 'canopy') {
            if ($agentId) {
                $brandAgent = BrandAgent::find($agentId);
                if ($brandAgent) {
                    $prompt = $brandAgent->instruction ?? $prompt;

                    if ($brandAgent->is_include && $brandId) {
                        $brand = Brand::find($brandId);
                        if ($brand) {
                            $prompt .= "\n\nHãy nhớ toàn bộ thông tin về thương hiệu bên dưới để tạo câu trả lời phù hợp:\n";

                            $rootData = $brand->root_data ?? [];
                            $trunkData = $brand->trunk_data ?? [];

                            if (is_array($rootData)) {
                                foreach ($rootData as $val) {
                                    if (is_string($val) && !empty($val))
                                        $prompt .= $val . "\n\n";
                                }
                            }
                            if (is_array($trunkData)) {
                                foreach ($trunkData as $val) {
                                    if (is_string($val) && !empty($val))
                                        $prompt .= $val . "\n\n";
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $agentConfig = null;
            if ($agentId) {
                $agentConfig = AgentSystem::find($agentId);
            }
            if (!$agentConfig && $agentType) {
                $agentConfig = AgentSystem::where('agent_type', $agentType)->first();
            }

            if ($agentConfig) {
                $prompt = $agentConfig->prompt ?? $prompt;
            }
        }

        $sequence = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
        $currentIndex = array_search($agentType, $sequence);

        if ($brandId && $currentIndex !== false && $currentIndex > 0) {
            $brand = Brand::find($brandId);
            if ($brand) {
                $rootData = $brand->root_data ?? [];
                $trunkData = $brand->trunk_data ?? [];
                $contextParts = [];

                for ($i = 0; $i < $currentIndex; $i++) {
                    $prevType = $sequence[$i];
                    $val = null;

                    if (str_starts_with($prevType, 'root')) {
                        $val = $rootData[$prevType] ?? null;
                    } elseif (str_starts_with($prevType, 'trunk')) {
                        $val = $trunkData[$prevType] ?? null;
                    }

                    if (!empty($val)) {
                        $contextParts[] = "$prevType: $val";
                    }
                }

                if (!empty($contextParts)) {
                    $prompt .= "\n\nHãy ghi nhớ các thông tin thương hiệu đã xác nhận bên dưới để tạo câu trả lời tiếp theo phù hợp:\n" . implode("\n", $contextParts);
                }
            }
        }

        // --- RAG: Vector Store Search ---
        $vectorId = null;

        // Lấy vector_id từ agent (BrandAgent hoặc AgentSystem)
        if ($agentType === 'canopy' && isset($brandAgent) && $brandAgent) {
            $vectorId = $brandAgent->vector_id ?? null;
        } elseif (isset($agentConfig) && $agentConfig) {
            $vectorId = $agentConfig->vector_id ?? null;
        }

        // Nếu có vector_id, gọi OpenAI Vector Store Search API
        if (!empty($vectorId)) {
            $ragContent = $this->searchVectorStore($vectorId, $userInput);
            if (!empty($ragContent)) {
                $prompt .= "\n\nCó thể tham khảo các tài liệu mẫu bên dưới, cho phép tự quyết định có sử dụng thông tin bên dưới hoặc không sử dụng tùy vào sự phù hợp. Các thông tin liên quan là:\n" . $ragContent;
            }
        }
        // --- End RAG ---

        // --- End Prompt Building Logic ---

        // 1. Create or Find Conversation
        if (!$convId || $convId === 'new') {
            $chat = $this->createConversation($agentType, $agentId, $userInput, $brandId, $userId);
            $convId = $chat->id;
        } else {
            $chat = Chat::where('id', $convId)->where('brand_id', $brandId)->first();
            if (!$chat) {
                $chat = $this->createConversation($agentType, $agentId, $userInput, $brandId, $userId);
                $convId = $chat->id;
            }
        }

        // 2. Save User Message
        Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userInput
        ]);

        // 3. Prepare Gemini History
        $messages = Message::where('chat_id', $chat->id)->orderBy('created_at', 'asc')->get();
        $contents = [];
        foreach ($messages as $msg) {
            if (empty($msg->content))
                continue;
            $role = ($msg->role === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg->content]
                ]
            ];
        }

        // 4. Stream Response from Gemini
        return response()->stream(function () use ($chat, $prompt, $contents) {
            // Disable output buffering
            while (ob_get_level()) {
                ob_end_flush();
            }

            $apiKey = env('GEMINI_API_KEY');

            // Check API Key
            if (empty($apiKey)) {
                $errData = ['type' => 'response.output_text.delta', 'delta' => '[Lỗi: GEMINI_API_KEY chưa được cấu hình trong .env]'];
                echo "data: " . json_encode($errData) . "\n\n";
                flush();
                return;
            }

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:streamGenerateContent?alt=sse&key=' . $apiKey;

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ],
                'contents' => $contents
            ];

            // Send initial metadata
            echo json_encode(['db_chat_id' => $chat->id, 'event' => 'metadata', 'conv_id' => $chat->id]) . "\n\n";
            flush();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $buffer = '';
            $hasError = false;

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$buffer, &$hasError) {
                $buffer .= $chunk;

                // Process complete lines
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);
                    $line = trim($line);

                    if (empty($line))
                        continue;

                    // SSE data line
                    if (strpos($line, 'data: ') === 0) {
                        $json = substr($line, 6);
                        if ($json === '[DONE]')
                            continue;

                        $data = json_decode($json, true);

                        // Check for error in response
                        if (isset($data['error'])) {
                            $hasError = true;
                            $errMsg = $data['error']['message'] ?? 'Unknown error';
                            $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi Gemini: $errMsg]"];
                            echo "data: " . json_encode($errData) . "\n\n";
                            flush();
                            continue;
                        }

                        // Extract text content
                        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];
                            $outData = [
                                'type' => 'response.output_text.delta',
                                'delta' => $text
                            ];
                            echo "data: " . json_encode($outData) . "\n\n";
                            flush();
                        }
                    }
                    // Check for raw JSON error (non-SSE error response)
                    elseif ($line[0] === '{') {
                        $data = json_decode($line, true);
                        if (isset($data['error'])) {
                            $hasError = true;
                            $errMsg = $data['error']['message'] ?? json_encode($data['error']);
                            $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi API: $errMsg]"];
                            echo "data: " . json_encode($errData) . "\n\n";
                            flush();
                        }
                    }
                }
                return strlen($chunk);
            });

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                $errorMsg = curl_error($ch);
                Log::error('Gemini Curl Error: ' . $errorMsg);
                $errData = ['type' => 'response.output_text.delta', 'delta' => "[Lỗi kết nối: $errorMsg]"];
                echo "data: " . json_encode($errData) . "\n\n";
                flush();
            }

            curl_close($ch);

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    private function createConversation($agentType, $agentId, $firstMessage, $brandId, $userId)
    {
        return Chat::create([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'agent_id' => (int) $agentId,
            'agent_type' => $agentType,
            'model' => 'Gemini',
            'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
            'conversation_id' => 'gemini_' . uniqid() . '_' . Str::random(8)
        ]);
    }

    /**
     * Gọi OpenAI Vector Store Search API để lấy top 5 chunks liên quan
     */
    private function searchVectorStore($vectorId, $query)
    {
        $apiKey = env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            Log::warning('OPENAI_API_KEY not set for vector search');
            return '';
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post("https://api.openai.com/v1/vector_stores/{$vectorId}/search", [
                    'query' => $query,
                    'max_num_results' => 5
                ]);

            if (!$response->successful()) {
                Log::error('Vector Store Search Error: ' . $response->body());
                return '';
            }

            $data = $response->json();
            $chunks = $data['data'] ?? [];

            if (empty($chunks)) {
                return '';
            }

            // Format chunks với điểm phù hợp
            $formattedChunks = [];
            foreach ($chunks as $index => $chunk) {
                $score = $chunk['score'] ?? 0;
                $scorePercent = round($score * 100, 1);
                $filename = $chunk['filename'] ?? 'unknown';

                // Lấy nội dung text từ content array
                $textContent = '';
                if (isset($chunk['content']) && is_array($chunk['content'])) {
                    foreach ($chunk['content'] as $contentPart) {
                        if (isset($contentPart['text'])) {
                            $textContent .= $contentPart['text'] . ' ';
                        }
                    }
                }
                $textContent = trim($textContent);

                if (!empty($textContent)) {
                    $formattedChunks[] = "[Chunk " . ($index + 1) . " - Điểm phù hợp: {$scorePercent}% - File: {$filename}]\n{$textContent}";
                }
            }

            return implode("\n\n", $formattedChunks);

        } catch (\Exception $e) {
            Log::error('Vector Store Search Exception: ' . $e->getMessage());
            return '';
        }
    }
}
