<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Support\Str; // For slug generation later in the resource

class NewsCategory extends Model
{
    use HasFactory;

    protected $table = 'news_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Define relationships for created_by and updated_by users
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
        return 'slug'; // Use slug for route model binding for categories
    }
}