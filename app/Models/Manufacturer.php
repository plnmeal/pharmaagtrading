<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Manufacturer extends Model
{
    use HasFactory;

    protected $table = 'manufacturers'; // Explicitly set the table name

    // Define which attributes are mass assignable
    protected $fillable = [
        'name',
        'description',
        'logo_path',
        'website_url',
        'order',
        'is_active',
        'is_featured',
        'created_by', // Add these for mass assignment if you're setting them manually
        'updated_by', // (Though often handled automatically by Filament or observers)
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