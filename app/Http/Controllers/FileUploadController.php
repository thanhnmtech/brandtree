<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\RagServiceInterface;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý upload và quản lý file cho RAG
 */
class FileUploadController extends Controller
{
    private RagServiceInterface $ragService;

    public function __construct(RagServiceInterface $ragService)
    {
        $this->ragService = $ragService;
    }

    /**
     * Upload file cho Chat
     * POST /api/files/upload
     * 
     * Body:
     * - file: File cần upload
     * - chat_id: ID của chat
     */
    public function uploadForChat(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:' . (config('rag.upload.max_file_size', 10485760) / 1024), // KB
            'chat_id' => 'nullable', // Cho phép rỗng khi là chat mới
            'agent_type' => 'nullable|string',
            'agent_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
        ]);

        $chatId = $request->input('chat_id');
        $attachableType = 'App\\Models\\Chat';
        $isNewChat = false;

        // Nếu chat_id rỗng hoặc 'new' → tạo Chat mới
        if (empty($chatId) || $chatId === 'new' || !is_numeric($chatId)) {
            $userId = auth()->id();
            $brandId = $request->input('brand_id');
            $agentType = $request->input('agent_type');
            $agentId = $request->input('agent_id');

            if (!$brandId) {
                return response()->json(['error' => 'Thiếu brand_id khi tạo chat mới'], 422);
            }

            // Tạo Chat mới (tương tự GeminiChatController::createConversation)
            $chat = \App\Models\Chat::create([
                'user_id' => $userId,
                'brand_id' => $brandId,
                'agent_id' => (int) $agentId,
                'agent_type' => $agentType,
                'model' => 'Gemini', // Mặc định, sẽ lock sau tin nhắn đầu
                'title' => 'Phiên làm việc ' . date('Y/m/d H:i:s'),
                'conversation_id' => 'upload_' . uniqid() . '_' . \Illuminate\Support\Str::random(8),
            ]);

            $chatId = $chat->id;
            $isNewChat = true;

            Log::channel('rag')->info("Auto-created chat {$chatId} for file upload by user {$userId}");
        } else {
            // Chat_id có sẵn → tìm chat
            $chat = \App\Models\Chat::find($chatId);
            if (!$chat) {
                return response()->json(['error' => 'Chat không tồn tại'], 404);
            }
        }

        // Kiểm tra giới hạn số file
        if (!$this->ragService->canUploadMore($attachableType, $chatId)) {
            return response()->json([
                'error' => 'Đã đạt giới hạn ' . config('rag.upload.max_files_per_context', 20) . ' file'
            ], 422);
        }

        // Kiểm tra MIME type
        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $allowedTypes = config('rag.upload.allowed_mime_types', []);

        if (!in_array($mimeType, $allowedTypes)) {
            $uId = auth()->id();
            Log::channel('rag')->warning("Upload rejected: Unsupported MIME type '$mimeType' for user $uId (Chat $chatId)");
            return response()->json([
                'error' => 'Loại file không được hỗ trợ: ' . $mimeType
            ], 422);
        }

        try {
            // Tạo cấu trúc thư mục: uploads/brand_{brandId}/user_{userId}
            $brandId = $chat->brand_id;
            $userId = auth()->id();
            $directory = "uploads/brand_{$brandId}/user_{$userId}";

            // Đổi tên file: filename-timestamp.ext
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $newFilename = $filename . '-' . time() . '.' . $extension;

            // Lưu file
            $path = $file->storeAs($directory, $newFilename);

            // Xử lý file (async)
            $result = $this->ragService->processFile($path, $mimeType, $attachableType, $chatId);

            return response()->json([
                'success' => true,
                'file_id' => $result['file_id'],
                'filename' => $file->getClientOriginalName(),
                'status' => $result['status'],
                'chat_id' => $chatId,        // Trả về chat_id (quan trọng khi tạo mới)
                'is_new_chat' => $isNewChat,  // Cho frontend biết cần cập nhật URL
            ]);

        } catch (\Exception $e) {
            Log::channel('rag')->error("Upload failed for chat $chatId: " . $e->getMessage());
            Log::error("Lỗi upload file cho chat: " . $e->getMessage());
            return response()->json(['error' => 'Lỗi upload file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload file cho BrandAgent
     * POST /brands/{brand}/agents/{agent}/files
     * 
     * Body:
     * - file: File cần upload
     */
    public function uploadForAgent(Request $request, $brandSlug, $agentId)
    {
        $request->validate([
            'file' => 'required|file|max:' . (config('rag.upload.max_file_size', 10485760) / 1024),
        ]);

        $attachableType = 'App\\Models\\BrandAgent';

        // Kiểm tra agent tồn tại
        $agent = \App\Models\BrandAgent::find($agentId);
        if (!$agent) {
            return response()->json(['error' => 'Agent không tồn tại'], 404);
        }

        // Kiểm tra giới hạn số file
        if (!$this->ragService->canUploadMore($attachableType, $agentId)) {
            return response()->json([
                'error' => 'Đã đạt giới hạn ' . config('rag.upload.max_files_per_context', 20) . ' file'
            ], 422);
        }

        // Kiểm tra MIME type - Agent chỉ chấp nhận PDF, Word, TXT (không chấp nhận image)
        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $agentAllowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ];

        if (!in_array($mimeType, $agentAllowedTypes)) {
            return response()->json([
                'error' => 'Loại file không được hỗ trợ. Chỉ chấp nhận PDF, DOC, DOCX, TXT.'
            ], 422);
        }

        try {
            // Tạo cấu trúc thư mục: uploads/brand_{brandId}/user_{userId}
            $brandId = $agent->brand_id;
            $userId = auth()->id();
            $directory = "uploads/brand_{$brandId}/user_{$userId}";

            // Đổi tên file: filename-timestamp.ext
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $newFilename = $filename . '-' . time() . '.' . $extension;

            // Lưu file
            $path = $file->storeAs($directory, $newFilename);

            // Xử lý file (async)
            $result = $this->ragService->processFile($path, $mimeType, $attachableType, $agentId);

            return response()->json([
                'success' => true,
                'file_id' => $result['file_id'],
                'filename' => $file->getClientOriginalName(),
                'status' => $result['status'],
            ]);

        } catch (\Exception $e) {
            Log::channel('rag')->error("Upload failed for agent $agentId: " . $e->getMessage());
            Log::error("Lỗi upload file cho agent: " . $e->getMessage());
            return response()->json(['error' => 'Lỗi upload file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Kiểm tra status của file
     * GET /api/files/{file}/status
     */
    public function status($fileId)
    {
        $result = $this->ragService->getFileStatus((int) $fileId);

        if ($result['status'] === 'not_found') {
            return response()->json(['error' => 'File không tồn tại'], 404);
        }

        return response()->json($result);
    }

    /**
     * Xóa file
     * DELETE /api/files/{file}
     */
    public function destroy($fileId)
    {
        // Kiểm tra quyền (chỉ owner mới được xóa)
        $file = UploadedFile::find($fileId);

        if (!$file) {
            return response()->json(['error' => 'File không tồn tại'], 404);
        }

        if ($file->user_id !== auth()->id()) {
            return response()->json(['error' => 'Không có quyền xóa file này'], 403);
        }

        $success = $this->ragService->deleteFile((int) $fileId);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Đã xóa file']);
        }

        return response()->json(['error' => 'Không thể xóa file'], 500);
    }

    /**
     * Lấy danh sách file của chat
     * GET /api/files/chat/{chatId}
     */
    public function listForChat($chatId)
    {
        $files = $this->ragService->getFiles('App\\Models\\Chat', (int) $chatId);
        return response()->json(['files' => $files]);
    }

    /**
     * Lấy danh sách file của agent
     * GET /api/files/agent/{agentId}
     */
    public function listForAgent($agentId)
    {
        $files = $this->ragService->getFiles('App\\Models\\BrandAgent', (int) $agentId);
        return response()->json(['files' => $files]);
    }
}
