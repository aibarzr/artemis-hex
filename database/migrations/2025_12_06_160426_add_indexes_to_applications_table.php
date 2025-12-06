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
        Schema::table('applications', function (Blueprint $table) {
            // Index for filtering and grouping by evaluator
            // Used in: getConsolidatedList(), countByEvaluator()
            $table->index('evaluator_id', 'idx_applications_evaluator_id');

            // Index for unique email lookup and duplicate prevention
            // Used in: findByEmail()
            $table->index('candidate_email', 'idx_applications_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_applications_evaluator_id');
            $table->dropIndex('idx_applications_email');
        });
    }
};
