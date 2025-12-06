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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('candidate_email');
            $table->string('position');
            $table->integer('years_of_experience');
            $table->string('status')->default('pending');
            $table->string('cv_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('evaluators')->nullOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index('candidate_email');
            $table->index('status');
            $table->index('evaluator_id');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
