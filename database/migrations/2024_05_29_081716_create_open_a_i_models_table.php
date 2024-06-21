<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('open_a_i_models', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('value');
            $table->double('temp');
            $table->text('openai_prompt');
            $table->json('json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_a_i_models');
    }
};
