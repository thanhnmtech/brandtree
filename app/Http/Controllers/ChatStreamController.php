<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Models\AgentSystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

class ChatStreamController extends Controller
{

    public function stream(Request $request)
    {
        $convId = $request->input('convId');
        $userInput = $request->input('message');
        $agentType = $request->input('agentType');
        $agentId = $request->input('agentId');
        $brandId = $request->input('brandId');
        $userId = $request->user() ? $request->user()->id : null;

        // Logic: Ưu tiên tìm theo ID trước, nếu không có thì tìm theo Type
        // Default values
        $prompt = "Bạn là trợ lý ảo.";
        $vectorStoreId = "";
        $aiModel = "gpt-4o";

        if ($agentType === 'canopy') {
            // Handle Custom Brand Agent
            if ($agentId) {
                $brandAgent = \App\Models\BrandAgent::find($agentId);
                if ($brandAgent) {
                    $prompt = $brandAgent->instruction ?? $prompt;
                    $vectorStoreId = $brandAgent->vector_id ?? "";

                    if ($brandAgent->is_include && $brandId) {
                        $brand = \App\Models\Brand::find($brandId);
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
            // Handle System Agent (Original Logic)
            $agentConfig = null;

            if ($agentId) {
                $agentConfig = AgentSystem::find($agentId);
            }

            if (!$agentConfig && $agentType) {
                $agentConfig = AgentSystem::where('agent_type', $agentType)->first();
            }

            if ($agentConfig) {
                $prompt = $agentConfig->prompt ?? $prompt;
                $vectorStoreId = $agentConfig->vector_id ?? $vectorStoreId;
                $aiModel = $agentConfig->model ?? $aiModel;
            }
        }

        // Logic accumulated context from previous steps
        $sequence = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
        $currentIndex = array_search($agentType, $sequence);

        if ($brandId && $currentIndex !== false && $currentIndex > 0) {
            $brand = \App\Models\Brand::find($brandId);
            if ($brand) {
                $rootData = $brand->root_data ?? [];
                $trunkData = $brand->trunk_data ?? [];
                $contextParts = [];

                // Iterate through all previous steps
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

        // 1. Create Conversation if new
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

        $conversationId = $chat->conversation_id;

        // Save User Message
        Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userInput
        ]);

        // 2. Stream Response
        return response()->stream(function () use ($conversationId, $userInput, $chat, $prompt, $vectorStoreId, $aiModel) {
            $apiKey = env('OPENAI_API_KEY');
            $url = 'https://api.openai.com/v1/responses';

            $data = [
                'model' => $aiModel,
                'instructions' => $prompt,
                'input' => $userInput,
                'conversation' => $conversationId,
                'stream' => true,
            ];

            if ($vectorStoreId) {
                $data['tools'] = [
                    [
                        'type' => 'file_search',
                        'vector_store_ids' => [$vectorStoreId],
                        'max_num_results' => 20
                    ]
                ];
            }

            // Send initial JSON with conversation details
            echo json_encode(['db_chat_id' => $chat->id, 'event' => 'metadata', 'conv_id' => $chat->id]) . "\n\n";
            if (ob_get_level() > 0)
                ob_flush();
            flush();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) {
                echo $chunk;
                if (ob_get_level() > 0)
                    ob_flush();
                flush();
                return strlen($chunk);
            });

            curl_exec($ch);
            curl_close($ch);

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    // Endpoint to save assistant message after streaming completes
    public function saveMessage(Request $request)
    {
        $chatId = $request->input('chat_id');
        $content = $request->input('content');

        if ($chatId && $content) {
            Message::create([
                'chat_id' => $chatId,
                'role' => 'assistant',
                'content' => $content
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 400);
    }

    private function createConversation($agentType, $agentId, $firstMessage, $brandId, $userId)
    {
        $apiKey = env('OPENAI_API_KEY');

        $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/conversations', [
            'metadata' => [
                'agentType' => $agentType,
                'agentId' => (string) $agentId,
                'topic' => substr($firstMessage, 0, 50)
            ]
        ]);

        $openAiConvId = $response->json()['id'] ?? null;

        if (!$openAiConvId) {
            Log::error('OpenAI Conversation creation failed', $response->json());
        }

        return Chat::create([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'agent_id' => (int) $agentId,
            'agent_type' => $agentType,
            'model' => 'ChatGPT',
            'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
            'conversation_id' => $openAiConvId
        ]);
    }

    public function history(Request $request)
    {
        $brandId = $request->input('brandId');
        $agentId = $request->input('agentId');
        $agentType = $request->input('agentType');
        $userId = $request->user()->id;

        if (!$brandId || !$agentId) {
            return response()->json(['data' => []]);
        }

        $query = Chat::where('brand_id', $brandId)
            ->where('agent_id', $agentId)
            ->where('user_id', $userId);

        if ($agentType) {
            $query->where('agent_type', $agentType);
        }

        $chats = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($chats);
    }

    /**
     * Đổi tên đoạn chat
     * PUT /api/chat/{id}/rename
     */
    public function rename(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        // Tìm chat theo id
        $chat = Chat::findOrFail($id);

        // Kiểm tra quyền: chỉ chủ sở hữu mới được đổi tên
        if ($chat->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền chỉnh sửa'], 403);
        }

        // Cập nhật title
        $chat->title = $request->title;
        $chat->save();

        return response()->json([
            'success' => true,
            'title' => $chat->title
        ]);
    }
}

