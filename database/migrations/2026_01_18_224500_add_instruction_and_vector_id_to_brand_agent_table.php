<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('brand_agent', function (Blueprint $table) {
            $table->text('instruction')->nullable()->after('output_description');
            $table->string('vector_id', 255)->nullable()->after('instruction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_agent', function (Blueprint $table) {
            $table->dropColumn(['instruction', 'vector_id']);
        });
    }
};
