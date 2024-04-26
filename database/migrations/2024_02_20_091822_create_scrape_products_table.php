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
        Schema::create('scrape_products', function (Blueprint $table) {
            $table->id();
            $table->longText('title');
            $table->string('asin');
            $table->string('priceUnit');
            $table->string('unit');
            $table->string('price');
            $table->string('image');
            $table->json('colorVariations');
            $table->json('brandDetails');
            $table->json('dimension');
            $table->json('detailInfo');
            $table->text('description');
            $table->text('about_this_item');
            $table->string('shippingCost');
            $table->string('code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_products');
    }
};
