<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL; // Import URL facade for route generation

class NavigationItem extends Model
{
    use HasFactory;

    protected $table = 'navigation_items';

    protected $fillable = [
        'label',
        'type',
        'custom_url',
        'page_id',
        'news_category_id',
        'therapeutic_category_id',
        'dosage_form_id',
        'location',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Define relationships
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class);
    }

    public function therapeuticCategory(): BelongsTo
    {
        return $this->belongsTo(TherapeuticCategory::class);
    }

    public function dosageForm(): BelongsTo
    {
        return $this->belongsTo(DosageForm::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the URL for the navigation item based on its type.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        switch ($this->type) {
            case 'page':
                return $this->page ? route('pages.show', $this->page->slug) : '#';
            case 'news_category':
                return $this->newsCategory ? route('news.index', ['category' => $this->newsCategory->slug]) : '#'; // Assuming news.index can filter by category slug
            case 'therapeutic_category':
                return $this->therapeuticCategory ? route('products.index', ['category' => $this->therapeuticCategory->name]) : '#'; // Assuming products.index can filter by category name
            case 'dosage_form':
                return $this->dosageForm ? route('products.index', ['dosageForm' => $this->dosageForm->name]) : '#'; // Assuming products.index can filter by dosage form name
            case 'homepage_section': // For anchors on the homepage
                return url('/') . $this->custom_url; // e.g., custom_url = '#network'
            case 'products_index': // Specific link to products page
                return route('products.index');
            case 'news_index': // Specific link to news page
                return route('news.index');
            case 'contact_index': // Specific link to contact page
                return route('contact.index');
            case 'home_index': // Specific link to home page
                return url('/');
            case 'custom_url':
            default:
                return $this->custom_url ?: '#';
        }
    }
}