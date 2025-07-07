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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // For clean URLs (e.g., /about-us, /privacy-policy)
            $table->longText('content')->nullable(); // Main page content (rich text)
            $table->string('featured_image_path')->nullable(); // Main image for the page (e.g., banner)

            // SEO Meta Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_active')->default(true); // Published/Draft status

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
        Schema::dropIfExists('pages');
    }
};