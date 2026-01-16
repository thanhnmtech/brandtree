<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update "Gói Nền tảng" with yearly prices
        DB::table('plans')
            ->where('slug', 'nen-tang')
            ->update([
                'yearly_price' => 9900000, // 10 tháng thay vì 12
                'yearly_original_price' => 11880000, // 12 tháng giá gốc
            ]);

        // Update "Gói Chuyên nghiệp" with yearly prices
        DB::table('plans')
            ->where('slug', 'chuyen-nghiep')
            ->update([
                'yearly_price' => 19900000, // 10 tháng thay vì 12
                'yearly_original_price' => 23880000, // 12 tháng giá gốc
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plans')
            ->whereIn('slug', ['nen-tang', 'chuyen-nghiep'])
            ->update([
                'yearly_price' => null,
                'yearly_original_price' => null,
            ]);
    }
};
