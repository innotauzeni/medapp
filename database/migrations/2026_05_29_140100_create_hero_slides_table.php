<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('badge')->nullable();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('secondary_cta_label')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->string('background_color', 20)->nullable();
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
