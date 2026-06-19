<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('paynowtransactions') && !Schema::hasTable('paynow_transactions')) {
            Schema::rename('paynowtransactions', 'paynow_transactions');
        }

        if (!Schema::hasTable('paynow_transactions')) {
            return;
        }

        Schema::table('paynow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('paynow_transactions', 'book_payment_id')) {
                $table->foreignId('book_payment_id')
                    ->nullable()
                    ->constrained('book_payments')
                    ->nullOnDelete()
                    ->after('payment_channel_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('paynow_transactions') && Schema::hasColumn('paynow_transactions', 'book_payment_id')) {
            Schema::table('paynow_transactions', function (Blueprint $table) {
                $table->dropForeign(['book_payment_id']);
                $table->dropColumn('book_payment_id');
            });
        }
    }
};
