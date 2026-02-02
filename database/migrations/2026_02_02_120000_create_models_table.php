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
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Tên định danh model (vd: gpt-4)');
            $table->string('title')->comment('Tên hiển thị (vd: GPT-4 Turbo)');
            $table->decimal('price_input', 15, 9)->default(0)->comment('Giá input token');
            $table->decimal('price_output', 15, 9)->default(0)->comment('Giá output token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
