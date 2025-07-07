<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

// Import all related models for relationships:
use App\Models\Manufacturer;
use App\Models\DosageForm; // <-- ADD THIS LINE
use App\Models\TherapeuticCategory;
use App\Models\User; // For created_by/updated_by

use Illuminate\Support\Str; // For slug generation and snippets (if used in model)

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'manufacturer_id',
        'dosage_form_id',
        'therapeutic_category_id',
        'description',
        'benefits',
        'ingredients',
        'availability_status',
        'product_image_path',
        'order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Cast attributes
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Define relationships
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function dosageForm(): BelongsTo
    {
        return $this->belongsTo(DosageForm::class);
    }

    public function therapeuticCategory(): BelongsTo
    {
        return $this->belongsTo(TherapeuticCategory::class);
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
}