<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Counter table that issues incremental certificate numbers.
 *
 * A single row holds the current 2-letter prefix and 6-digit counter.
 * Numbers render as PREFIX + 6 digits, e.g. AA000001, AA000002 ...
 * When the counter passes 999999 the prefix rolls over (AA -> AB ...),
 * so AA999999 is followed by AB000000.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificate_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 2)->default('AA');
            $table->unsignedInteger('current_number')->default(0);
            $table->timestamps();
        });

        DB::table('certificate_sequences')->insert([
            'prefix'         => 'AA',
            'current_number' => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_sequences');
    }
};
