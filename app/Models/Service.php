<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'icon', 'title', 'description', 'order_index', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
