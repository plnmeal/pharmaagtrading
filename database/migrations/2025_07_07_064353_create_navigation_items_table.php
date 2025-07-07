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
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // Text displayed in the menu (e.g., "About Us")
            $table->string('type')->default('custom_url'); // 'page', 'news_category', 'product_category', 'custom_url', 'homepage_section'
            $table->string('custom_url')->nullable(); // For 'custom_url' type
            $table->foreignId('page_id')->nullable()->constrained('pages')->onDelete('set null'); // For 'page' type
            $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->onDelete('set null'); // For 'news_category' type
            $table->foreignId('therapeutic_category_id')->nullable()->constrained('therapeutic_categories')->onDelete('set null'); // For 'product_category' type (therapeutic)
            $table->foreignId('dosage_form_id')->nullable()->constrained('dosage_forms')->onDelete('set null'); // For 'product_category' type (dosage)

            $table->string('location')->default('header'); // 'header', 'footer_navigate', 'footer_legal', 'footer_social' (though social is different)
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('navigation_items');
    }
};