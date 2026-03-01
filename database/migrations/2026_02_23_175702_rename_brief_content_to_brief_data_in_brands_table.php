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
            $table->renameColumn('root_brief_content', 'root_brief_data');
            $table->renameColumn('trunk_brief_content', 'trunk_brief_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->renameColumn('root_brief_data', 'root_brief_content');
            $table->renameColumn('trunk_brief_data', 'trunk_brief_content');
        });
    }
};
