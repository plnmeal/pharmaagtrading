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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Link to product, delete images if product is deleted
            $table->string('path'); // Path to the image file (e.g., product-images/product_id/image_name.jpg)
            $table->string('alt_text')->nullable(); // Alt text for SEO and accessibility
            $table->integer('order')->default(0); // Display order for images
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};