<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Support\Str; // For slug generation and potentially snippets

class NewsArticle extends Model
{
    use HasFactory;

    protected $table = 'news_articles';

    protected $fillable = [
        'news_category_id',
        'title',
        'slug',
        'featured_image_path',
        'snippet',
        'content',
        'published_at',
        'is_active',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    // Cast 'published_at' to a datetime object
    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Define relationships
    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class);
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
     * Get the route key for the model.
     * Allows using 'slug' in route model binding instead of ID.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Optional: Mutator to generate snippet from content if not provided
    // public function setSnippetAttribute($value)
    // {
    //     $this->attributes['snippet'] = $value ?: Str::limit(strip_tags($this->content), 150);
    // }
}