<?php
namespace App\Console\Commands;

use App\Models\Brand;
use Illuminate\Console\Command;

class CheckBriefData extends Command
{
    protected $signature = 'app:check-brief-data';
    protected $description = 'Check brief vs full content length in database';

    public function handle()
    {
        $brand = Brand::find(1);
        if (!$brand) {
            $this->error('Brand 1 not found');
            return 1;
        }

        $fullContent = $brand->root_data['root1'] ?? '';
        $briefContent = $brand->root_brief_data['root1'] ?? '';

        $this->info("Brand: {$brand->name} (ID: {$brand->id})");
        $this->info("Full content length: " . strlen($fullContent));
        $this->info("Brief content length: " . strlen($briefContent));
        
        if (strlen($briefContent) > 0) {
            $ratio = round(strlen($briefContent) / strlen($fullContent) * 100, 2);
            $this->info("Ratio: {$ratio}%");
            
            if ($ratio > 80) {
                $this->warn("⚠ Brief content is NOT actually summarized (>80% of full content)");
            } else {
                $this->info("✓ Brief content is properly summarized (<80% of full content)");
            }
        } else {
            $this->warn("Brief content is empty!");
        }

        return 0;
    }
}
