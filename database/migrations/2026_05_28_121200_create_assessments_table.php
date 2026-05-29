<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('course_module_id')->nullable()->constrained('course_modules')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['theory', 'practical', 'final'])->default('theory');
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->enum('outcome', ['pass', 'fail', 'pending'])->default('pending');
            $table->text('trainer_comments')->nullable();
            $table->date('assessed_on')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
