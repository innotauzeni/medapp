<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('id_type', ['id', 'passport'])->default('id');
            $table->string('id_number')->nullable()->index();
            $table->string('nationality')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->default('South Africa');

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            $table->string('employer')->nullable();
            $table->string('job_title')->nullable();

            $table->string('profile_photo_path')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
