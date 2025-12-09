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
        Schema::create('brand_agent', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('brand_id');

            // Columns
            $table->string('code', 255);
            $table->string('name', 255)->nullable();
            $table->text('target')->nullable();
            $table->text('output_description')->nullable();

            $table->string('assistant_id', 255)->nullable();
            $table->string('assistant_key', 255)->nullable();

            $table->text('prompt')->nullable();
            $table->longText('ui_display')->nullable();
            $table->integer('step_order')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_agent');
    }
};
