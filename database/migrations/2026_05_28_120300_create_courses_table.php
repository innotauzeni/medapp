<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_hours')->default(8);
            $table->unsignedSmallInteger('passing_score')->default(50);
            $table->decimal('fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
