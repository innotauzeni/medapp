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
        Schema::create('manual_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Booking::class)->constrained();
            $table->foreignIdFor(PaymentChannel::class)->constrained();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default("USD");
            $table->string('reference')->nullable();
            $table->integer('year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_transactions');
    }
};
