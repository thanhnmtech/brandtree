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
        Schema::create('system_agent', function (Blueprint $table) {
            $table->id();

            // Columns
            $table->boolean('is_template')->default(0);
            $table->enum('agent_type', ['root1', 'root2', 'root3','trunk1', 'trunk2'])->default('root1');
            $table->string('name', 255)->nullable();
            $table->text('target')->nullable();
            $table->text('output_description')->nullable();

            $table->string('assistant_id', 255)->nullable();
            $table->string('assistant_key', 255)->nullable();

            $table->text('prompt')->nullable();
            $table->longText('ui_display')->nullable(); // LONGTEXT

            $table->enum('status', ['locked', 'active', 'done'])->default('locked');
            $table->integer('step_order')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_agent');
    }
};
