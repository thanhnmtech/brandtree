<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

class ChatStreamController extends Controller
{
    private $instruction = "Bạn là chuyên gia thương hiệu";
    private $vector_store_id = "vs_696926c7e2d88191a163d14f71289579";
    private $model = "gpt-4o";

    public function stream(Request $request)
    {
        $convId = $request->input('convId');
        $userInput = $request->input('message');
        $agentType = $request->input('agentType');
        $agentId = $request->input('agentId');
        $brandId = $request->input('brandId');
        $userId = $request->user() ? $request->user()->id : null;

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
        return response()->stream(function () use ($conversationId, $userInput, $chat) {
            $apiKey = env('OPENAI_API_KEY');
            $url = 'https://api.openai.com/v1/responses';

            $data = [
                'model' => $this->model,
                'instructions' => $this->instruction,
                'tools' => [
                    [
                        'type' => 'file_search',
                        'vector_store_ids' => [$this->vector_store_id],
                        'max_num_results' => 20
                    ]
                ],
                'input' => $userInput,
                'conversation' => $conversationId,
                'stream' => true,
            ];

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
                'agentId' => $agentId,
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
            'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
            'conversation_id' => $openAiConvId
        ]);
    }
}

