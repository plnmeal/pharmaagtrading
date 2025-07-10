<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

// Import all related models for relationships:
use App\Models\Manufacturer;
use App\Models\DosageForm;
use App\Models\TherapeuticCategory;
use App\Models\User;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'name_es', // ADD THIS LINE
        'slug',
        'manufacturer_id',
        'dosage_form_id',
        'therapeutic_category_id',
        'description',
        'description_es', // ADD THIS LINE
        'benefits',
        'benefits_es', // ADD THIS LINE
        'ingredients',
        'ingredients_es', // ADD THIS LINE
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
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}