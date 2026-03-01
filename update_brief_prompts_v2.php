<?php
require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel::class')->bootstrap();

use App\Models\AgentSystem;

$updates = [
    'root1' => 'Summarize the provided company culture content into a BRIEF summary of 150-250 words maximum. Include only the most essential elements: (1) Core purpose and values (2) Key behavioral expectations (3) Main cultural highlights. Be concise and direct. Use bullet points if helpful. Do NOT expand or add new information.',
    
    'root2' => 'Summarize the provided market analysis content into a BRIEF summary of 150-250 words maximum. Include only: (1) Top market opportunity (2) Key target customer segment (3) Main requirement or risk. Be concise. Do NOT expand or add new details.',
    
    'root3' => 'Summarize the solution positioning into a BRIEF summary of 100-200 words maximum. Include ONLY: (1) Value proposition statement (one sentence) (2) Top competitive advantage (one sentence) (3) Target customer group (one sentence). Keep it extremely concise.',
    
    'trunk1' => 'Summarize the brand positioning into a BRIEF summary of 100-200 words maximum. Include ONLY: (1) Brand name & key identity (2) Core brand value (3) Main distinctive feature. Be very concise. No expansion.',
    
    'trunk2' => 'Summarize the communication guidelines into a BRIEF summary of 100-150 words maximum. Extract ONLY: (1) Primary tone/voice (2) Key unique words/terms (3) One main communication rule. Be extremely concise.',
];

foreach ($updates as $agentType => $briefPrompt) {
    AgentSystem::where('agent_type', $agentType)->update(['brief_prompt' => $briefPrompt]);
    echo "✓ Updated {$agentType}\n";
}

echo "\n✅ All brief_prompts updated with stricter summarization requirements!\n";
