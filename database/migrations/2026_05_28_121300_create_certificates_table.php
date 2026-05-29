<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('enrolment_id')->nullable()->constrained('enrolments')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->string('verification_code', 64)->unique();
            $table->enum('status', ['issued', 'revoked'])->default('issued');
            $table->text('revoked_reason')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificates')->cascadeOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->boolean('successful')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_verifications');
        Schema::dropIfExists('certificates');
    }
};
