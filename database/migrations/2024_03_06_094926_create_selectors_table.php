<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('selectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('selector');
            $table->string('type'); // e.g., 'id', 'class', 'tag'
            $table->string('status'); // e.g., 'enable', 'disable'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selectors');
    }
};
