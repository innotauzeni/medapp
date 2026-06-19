<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_channels')) {
            return;
        }

        Schema::table('payment_channels', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_channels', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (!Schema::hasColumn('payment_channels', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('slug');
            }

            if (!Schema::hasColumn('payment_channels', 'description')) {
                $table->string('description')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payment_channels')) {
            return;
        }

        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_active', 'description']);
        });
    }
};
