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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->string('local_model_id')->nullable();
            $table->string('open_ai_model_id');
            $table->string('image_model');
            $table->double('image_model_temperature',8,1);
            $table->string('key');
            $table->text('image_prompt');
            $table->boolean('is_image_compared')->default(0);
            $table->integer('log_delete_days');
            $table->string('timezone')->default('UTC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
