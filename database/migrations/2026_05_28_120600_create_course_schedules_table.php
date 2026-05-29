<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('lead_trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('capacity')->default(20);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('course_schedules')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['schedule_id', 'trainer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_trainers');
        Schema::dropIfExists('course_schedules');
    }
};
