<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new columns for yearly pricing (if not exist)
        if (!Schema::hasColumn('plans', 'yearly_price')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('yearly_price')->nullable()->after('original_price');
                $table->unsignedBigInteger('yearly_original_price')->nullable()->after('yearly_price');
            });
        }

        // Step 2: Migrate data from yearly plans to their parent monthly plans
        if (Schema::hasColumn('plans', 'parent_plan_id')) {
            $yearlyPlans = DB::table('plans')
                ->whereNotNull('parent_plan_id')
                ->where('billing_cycle', 'yearly')
                ->get();

            foreach ($yearlyPlans as $yearlyPlan) {
                DB::table('plans')
                    ->where('id', $yearlyPlan->parent_plan_id)
                    ->update([
                        'yearly_price' => $yearlyPlan->price,
                        'yearly_original_price' => $yearlyPlan->original_price,
                    ]);
            }

            // Step 3: Delete yearly plans (they are now merged into monthly plans)
            DB::table('plans')
                ->whereNotNull('parent_plan_id')
                ->delete();

            // Step 4: Drop foreign key if exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'plans'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME LIKE '%parent_plan_id%'
            ");

            if (!empty($foreignKeys)) {
                Schema::table('plans', function (Blueprint $table) {
                    $table->dropForeign(['parent_plan_id']);
                });
            }

            // Step 5: Drop old columns
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn(['billing_cycle', 'parent_plan_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add billing columns
        Schema::table('plans', function (Blueprint $table) {
            $table->enum('billing_cycle', ['monthly', 'yearly'])
                  ->nullable()
                  ->after('duration_days');

            $table->foreignId('parent_plan_id')
                  ->nullable()
                  ->after('billing_cycle')
                  ->constrained('plans')
                  ->nullOnDelete();
        });

        // Set all existing plans as monthly
        DB::table('plans')->update(['billing_cycle' => 'monthly']);

        // Drop yearly price columns
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['yearly_price', 'yearly_original_price']);
        });
    }
};
