<?php

namespace App\Http\Controllers;

use App\Models\ChatAiLog;
use Illuminate\Http\Request;

class LogViewerController extends Controller
{
    public function index()
    {
        if (!env('ENABLE_DEBUG_PAGES', true)) {
            abort(404);
        }

        $logs = ChatAiLog::with(['user', 'chat'])
            ->latest()
            ->cursorPaginate(30);

        return view('debug.chat_ai_logs', compact('logs'));
    }
}
