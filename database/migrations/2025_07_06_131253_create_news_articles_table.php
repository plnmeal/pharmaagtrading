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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->onDelete('set null'); // Link to categories
            $table->string('title');
            $table->string('slug')->unique(); // For clean URLs
            $table->string('featured_image_path')->nullable(); // Main image for the article
            $table->text('snippet')->nullable(); // Short summary for listings
            $table->longText('content'); // Full article content
            $table->dateTime('published_at')->nullable(); // For scheduled publishing

            $table->boolean('is_active')->default(true); // Published/Draft status
            $table->boolean('is_featured')->default(false); // To show on homepage preview

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};