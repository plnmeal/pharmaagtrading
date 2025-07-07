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
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            // Homepage Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image_path')->nullable(); // For the hero background image

            // Homepage CTA Section
            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();
            $table->string('cta_background_image_path')->nullable(); // For the CTA background image

            // Contact Information (from footer / contact page)
            $table->string('contact_address')->nullable();
            $table->string('contact_email')->nullable(); // Primary contact email
            $table->string('contact_phone')->nullable(); // If you want to add phone numbers

            // Social Links (from footer)
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();

            // General site info (from footer about text, meta tags)
            $table->string('site_name')->default('PharmaCorp'); // For logo text, page titles
            $table->text('site_description')->nullable(); // For meta description, footer about text

            // Add more fields as you identify manageable content on any page
            // Remember multilingual: for 'hero_title', we'll later have 'hero_title_en' and 'hero_title_es'
            // For now, keep it simple for the initial setup.

            $table->timestamps(); // created_at and updated_at
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