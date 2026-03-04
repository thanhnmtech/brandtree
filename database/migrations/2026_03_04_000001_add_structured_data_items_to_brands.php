<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Lưu structured items cho từng agent type
            // VD: root1_data_items = { "Purpose": "...", "Core Values": "...", ... }
            $table->json('root1_data_items')->nullable()->after('root_data');
            $table->json('root2_data_items')->nullable()->after('root1_data_items');
            $table->json('root3_data_items')->nullable()->after('root2_data_items');
            $table->json('trunk1_data_items')->nullable()->after('root3_data_items');
            $table->json('trunk2_data_items')->nullable()->after('trunk1_data_items');

            // Lưu brief items (cho nhanh access khi hiển thị)
            $table->json('root1_brief_items')->nullable()->after('trunk2_data_items');
            $table->json('root2_brief_items')->nullable()->after('root1_brief_items');
            $table->json('root3_brief_items')->nullable()->after('root2_brief_items');
            $table->json('trunk1_brief_items')->nullable()->after('root3_brief_items');
            $table->json('trunk2_brief_items')->nullable()->after('trunk1_brief_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Drop all new columns
            $table->dropColumn([
                'root1_data_items', 'root2_data_items', 'root3_data_items',
                'trunk1_data_items', 'trunk2_data_items',
                'root1_brief_items', 'root2_brief_items', 'root3_brief_items',
                'trunk1_brief_items', 'trunk2_brief_items',
            ]);
        });
    }
};
