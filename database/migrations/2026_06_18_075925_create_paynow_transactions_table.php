<?php

use App\Models\Booking;
use App\Models\PaymentChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paynow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Booking::class)->constrained();
            $table->foreignIdFor(PaymentChannel::class)->constrained();
            $table->foreignIdFor(\App\Models\BookPayment::class)->nullable()->constrained()->nullOnDelete();
            $table->string('uuid')->unique();
            $table->string('pollurl');
            $table->string('status')->default('PENDING');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paynow_transactions');
    }
};
