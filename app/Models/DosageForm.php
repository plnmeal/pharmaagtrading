<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DosageForm extends Model
{
    use HasFactory;

    protected $table = 'dosage_forms';

    protected $fillable = [
        'name',
        'name_es', // ADD THIS LINE
        'icon_class',
        'description',
        'description_es', // ADD THIS LINE
        'order',
        'is_active',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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
}