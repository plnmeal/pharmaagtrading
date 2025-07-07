<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Service extends Model
{
    use HasFactory;

    protected $table = 'services'; // Explicitly set the table name

    protected $fillable = [
        'title',
        'icon_class',
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
}