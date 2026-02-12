<?php

namespace App\Services;

use App\Models\ChatAiLog;
use Illuminate\Support\Facades\Log;

class ChatLoggingService
{
    /**
     * Log the chat interaction to database
     */
    public function log(
        ?int $chatId,
        ?int $userId,
        ?int $brandId,
        ?string $brandName,
        ?string $agentId,
        ?string $agentName,
        ?string $agentType,
        string $model,
        string $finalUserContent,
        string $finalSystemInstruction,
        ?int $inputTokens = null,
        ?int $outputTokens = null
    ): ?ChatAiLog {
        // Check if logging is enabled in config
        if (!config('app.enable_chat_logging') && !env('ENABLE_CHAT_LOGGING')) {
            return null;
        }

        try {
            return ChatAiLog::create([
                'chat_id' => $chatId,
                'user_id' => $userId,
                'brand_id' => $brandId,
                'brand_name' => $brandName,
                'agent_id' => $agentId,
                'agent_name' => $agentName,
                'agent_type' => $agentType,
                'model' => $model,
                'final_user_content' => $finalUserContent,
                'final_system_instruction' => $finalSystemInstruction,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ]);
        } catch (\Exception $e) {
            // Don't interrupt the chat flow if logging fails, just log the error
            Log::error('Failed to log chat interaction: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update the log with the AI's final response and token usage
     */
    public function updateLog(
        ?ChatAiLog $log,
        string $aiResponse,
        ?int $inputTokens = null,
        ?int $outputTokens = null
    ) {
        if (!$log) {
            return;
        }

        try {
            $updateData = ['ai_response' => $aiResponse];

            if ($inputTokens !== null) {
                $updateData['input_tokens'] = $inputTokens;
            }
            if ($outputTokens !== null) {
                $updateData['output_tokens'] = $outputTokens;
            }

            $log->update($updateData);
        } catch (\Exception $e) {
            Log::error('Failed to update chat log: ' . $e->getMessage());
        }
    }
}
