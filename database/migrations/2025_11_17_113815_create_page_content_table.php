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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->enum('type', ['page', 'ladipage','homepage']);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        Schema::create('page_content_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('page_content_id');
            $table->string('locale')->index();
            $table->string('title');
            $table->string('slug');
            $table->text('content');

            $table->unique(['page_content_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
