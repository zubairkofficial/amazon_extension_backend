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
        Schema::create('local_models', function (Blueprint $table) {
            $table->id();            
            $table->text('name');
            $table->string('type');
            $table->string('baseUrl');
            $table->integer('max_tokens')->nullable();
            $table->double('top_p')->nullable();
            $table->double('temp')->nullable();
            $table->integer('seed')->nullable();
            $table->string('mode')->nullable();
            $table->string('instruction_template')->nullable();
            $table->text('character')->nullable();            
            $table->text('prompt')->nullable();
            $table->json('json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_models');
    }
};
