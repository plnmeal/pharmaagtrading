<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class DosageForm extends Model
{
    use HasFactory;

    protected $table = 'dosage_forms'; // Explicitly set the table name

    protected $fillable = [
        'name',
        'icon_class',
        'description',
        'order',
        'is_active',
        'is_featured',
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
}