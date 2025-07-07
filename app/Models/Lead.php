<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'enquiry_type', // ADDED: Ensure this is fillable
        'is_read',
    ];

    // Cast attributes
    protected $casts = [
        'is_read' => 'boolean',
    ];
}