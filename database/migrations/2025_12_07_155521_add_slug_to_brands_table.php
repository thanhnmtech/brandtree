<?php

use App\Models\Brand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name')->nullable();
        });

        // Generate slugs for existing brands
        Brand::whereNull('slug')->each(function (Brand $brand) {
            $brand->slug = Str::slug($brand->name) . '-' . Str::lower(Str::random(6));
            $brand->save();
        });

        // Make slug not nullable after populating
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
