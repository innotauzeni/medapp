<?php
use App\Models\PaymentChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_channel_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentChannel::class)->constrained();
            $table->string('key');
            $table->string('value')->nullable();
            $table->timestamps();

            $table->unique(['payment_channel_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_channel_parameters');
    }
};
