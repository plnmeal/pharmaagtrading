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
        Schema::table('settings', function (Blueprint $table) {
            // Homepage Hero Section (English & Spanish)
            $table->string('hero_title_es')->nullable()->after('hero_title');
            $table->text('hero_subtitle_es')->nullable()->after('hero_subtitle');

            // Homepage CTA Section (English & Spanish)
            $table->string('cta_title_es')->nullable()->after('cta_title');
            $table->text('cta_description_es')->nullable()->after('cta_description');
            $table->string('cta_button_text_es')->nullable()->after('cta_button_text');

            // Contact Information (English & Spanish address)
            $table->string('contact_address_es')->nullable()->after('contact_address');
            // Email and Phone typically remain the same across languages, so no _es for them.

            // General site info (English & Spanish site name/description)
            $table->string('site_name_es')->nullable()->after('site_name'); // For logo text, page titles
            $table->text('site_description_es')->nullable()->after('site_description'); // For meta description, footer about text
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_es',
                'hero_subtitle_es',
                'cta_title_es',
                'cta_description_es',
                'cta_button_text_es',
                'contact_address_es',
                'site_name_es',
                'site_description_es',
            ]);
        });
    }
};