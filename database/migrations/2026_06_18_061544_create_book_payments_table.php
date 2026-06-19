<?php

use App\Models\PaymentChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignIdFor(PaymentChannel::class)->constrained();
            $table->decimal('total_ammount', 10, 2)->default(0);
            $table->string('uuid')->nullable();
            $table->string('pollurl')->nullable();
            $table->string('currency')->default("USD");
            $table->string('description')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_payments');
    }
};
