<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('course_schedules')->nullOnDelete();
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'failed', 'withdrawn'])->default('enrolled');
            $table->date('enrolled_on');
            $table->date('completed_on')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->text('trainer_comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'schedule_id'], 'enrolments_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
    }
};
