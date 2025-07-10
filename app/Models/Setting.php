<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        // English fields (existing)
        'hero_title', 'hero_subtitle', 'hero_image_path',        'subheader_text', // ADD THIS LINE (English)
        
        'cta_title', 'cta_description', 'cta_button_text', 'cta_button_link', 'cta_background_image_path',
        'contact_address', 'contact_email', 'contact_phone',
        'facebook_url', 'twitter_url', 'linkedin_url',
        'site_name', 'site_description',

        // Spanish fields (NEWLY ADDED)
        'hero_title_es', 'hero_subtitle_es', 'subheader_text_es', // ADD THIS LINE (Spanish)
        'cta_title_es', 'cta_description_es', 'cta_button_text_es',
        'contact_address_es',
        'site_name_es', 'site_description_es',
    ];
}