<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('icon')->nullable();
            $table->string('route_name')->nullable();
            $table->string('required_permission')->nullable();
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('app_submodules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_module_id')->constrained('app_modules')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('icon')->nullable();
            $table->string('route_name')->nullable();
            $table->string('required_permission')->nullable();
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['app_module_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_submodules');
        Schema::dropIfExists('app_modules');
    }
};
