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
        'label_es', // ADD THIS LINE
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
                return $this->newsCategory ? route('news.index', ['category' => $this->newsCategory->slug]) : '#';
            case 'therapeutic_category':
                return $this->therapeuticCategory ? route('products.index', ['category' => $this->therapeuticCategory->name]) : '#';
            case 'dosage_form':
                return $this->dosageForm ? route('products.index', ['dosageForm' => $this->dosageForm->name]) : '#';
            case 'homepage_section':
                return url('/') . $this->custom_url;
            case 'products_index':
                return route('products.index');
            case 'news_index':
                return route('news.index');
            case 'contact_index':
                return route('contact.index');
            case 'home_index':
                return url('/');
            case 'custom_url':
            default:
                return $this->custom_url ?: '#';
        }
    }
}