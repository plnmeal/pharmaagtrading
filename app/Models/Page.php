<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Support\Str; // For slug generation

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image_path',
        'meta_title',
        'meta_description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Cast attributes
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Define relationships
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
}