<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 32)->unique();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->enum('id_type', ['id', 'passport'])->default('id');
            $table->string('id_number')->nullable();
            $table->string('organization')->nullable();
            $table->string('city')->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', ['pending', 'contacted', 'confirmed', 'cancelled', 'converted'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->string('source', 30)->default('web');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1024)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('course_schedules')->nullOnDelete();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->text('comment')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_logs');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};
