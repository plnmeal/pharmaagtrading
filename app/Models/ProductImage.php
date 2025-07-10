<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // IMPORTANT: Import BelongsTo

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images'; // Explicitly set table name

    protected $fillable = [
        'product_id',
        'path',
        'alt_text',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Define relationship back to Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}