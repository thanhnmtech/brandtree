<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use App\Models\Message;
use App\Models\AgentSystem;
use Illuminate\Support\Facades\Log;

class Chat extends Component
{
    public $agentType;
    public $agentId;
    public $convId;
    public $brandId;
    public $messages = [];
    public $newMessage = '';

    // UI state
    public $title = 'Trợ lý ảo';
    public $description = 'Hỗ trợ tra cứu và phân tích dữ liệu.';
    public $isNew = false;

    public function mount($agentType = null, $agentId = null, $convId = null, $brandId = null)
    {
        $this->agentType = $agentType;
        $this->agentId = $agentId;
        $this->convId = $convId;
        $this->brandId = $brandId;

        // Fetch Agent Name
        if ($this->agentId) {
            $agent = AgentSystem::find($this->agentId);
            if ($agent) {
                $this->title = $agent->name;
            }
        }

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
        $chat = ChatModel::where('id', $this->convId)
            ->where('brand_id', $this->brandId)
            ->first();

        if ($chat) {
            $this->messages = $chat->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
            $this->title = $chat->title ?? $this->title;
        } else {
            // Chat not found or doesn't belong to brand
            $this->convId = 'new';
            $this->isNew = true;
            $this->messages = [];
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
