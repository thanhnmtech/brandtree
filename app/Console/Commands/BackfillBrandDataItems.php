<?php

// Thêm cái này vì mỗi lần chạy lại thì phải chạy backfill seeder trong db
// ==> cảm thấy nó không có thực tế --> nên đã implement command này
// sau này có thể setting để tự động backfill data mỗi lần update cái gì đó
// vì khi tạo mới và render lại, nếu ko được backfill thì data của brand không chịu hiển thị lên

// Note: đã thử dùng service nhưng hiện không có endpoint cho admin nên nghĩ là không nên làm theo cách này

namespace App\Console\Commands;

use App\Models\Brand;
use App\Services\BrandContentParser;
use Illuminate\Console\Command;

class BackfillBrandDataItems extends Command
{
    protected $signature = 'brands:backfill-data-items {--force}';

    protected $description = 'Backfill data_items cho các brand cũ từ root_data/trunk_data';

    public function handle()
    {
        if (! $this->option('force')) {
            $this->warn('⚠️ Command này sẽ parse lại tất cả dữ liệu hiện có.');
            if (! $this->confirm('Bạn chắc chắn muốn tiếp tục?')) {
                $this->info('Huỷ bỏ.');

                return;
            }
        }

        $brands = Brand::all();
        $processed = 0;
        $skipped = 0;

        $this->output->progressStart(count($brands));

        foreach ($brands as $brand) {
            try {
                // Xử lý root data
                $rootData = $brand->root_data ?? [];
                if (is_array($rootData)) {
                    foreach (['root1', 'root2', 'root3'] as $key) {
                        $content = $rootData[$key] ?? '';
                        if (! empty($content)) {
                            // Chỉ parse nếu items chưa tồn tại hoặc trống
                            $itemsColumn = "{$key}_data_items";
                            $existingItems = $brand->$itemsColumn ?? [];

                            if (empty($existingItems) || ! is_array($existingItems)) {
                                $parsedItems = BrandContentParser::parseContent($key, $content);
                                $brand->$itemsColumn = $parsedItems;
                                $processed++;
                            } else {
                                $skipped++;
                            }
                        }
                    }
                }

                // Xử lý trunk data
                $trunkData = $brand->trunk_data ?? [];
                if (is_array($trunkData)) {
                    foreach (['trunk1', 'trunk2'] as $key) {
                        $content = $trunkData[$key] ?? '';
                        if (! empty($content)) {
                            $itemsColumn = "{$key}_data_items";
                            $existingItems = $brand->$itemsColumn ?? [];

                            if (empty($existingItems) || ! is_array($existingItems)) {
                                $parsedItems = BrandContentParser::parseContent($key, $content);
                                $brand->$itemsColumn = $parsedItems;
                                $processed++;
                            } else {
                                $skipped++;
                            }
                        }
                    }
                }

                $brand->save();
            } catch (\Exception $e) {
                $this->warn("⚠️ Lỗi khi xử lý brand '{$brand->name}': ".$e->getMessage());
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("\nHoàn tất!");
        $this->line("Đang fetch: {$processed} | Skipped: {$skipped}");
    }
}
