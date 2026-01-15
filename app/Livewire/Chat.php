<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class Chat extends Component
{
    public $agentType;
    public $agentId;
    public $convId;
    public $messages = [];
    public $newMessage = '';

    // UI state
    public $title = 'Trợ lý ảo';
    public $description = 'Hỗ trợ tra cứu và phân tích dữ liệu.';
    public $isNew = false;

    public function mount($agentType = null, $agentId = null, $convId = null)
    {
        $this->agentType = $agentType;
        $this->agentId = $agentId;
        $this->convId = $convId;

        if ($convId && $convId !== 'new') {
            $this->loadMessages();
            $this->isNew = false;
        } else {
            $this->isNew = true;
        }

        // Just to ensure view gets variables
        // If agentType/id passed, maybe fetch Agent detail titles in real app
    }

    public function loadMessages()
    {
        $chat = ChatModel::find($this->convId);
        if ($chat) {
            $this->messages = $chat->messages()->orderBy('created_at', 'asc')->get()->toArray();
            $this->title = $chat->title ?? $this->title;
        } else {
            // Handle invalid ID case: reset to new?
            // For now, assume it might just be empty list
        }
    }

    // This is called by JS when streaming finishes logic decides it generated a "completed" message.
    // However, JS will call the API directly to save.
    // Livewire just needs to refresh or append to local state if helpful.
    // Or JS can dispatch event "message-saved".

    public function refreshMessages()
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
